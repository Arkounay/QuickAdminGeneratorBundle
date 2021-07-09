<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Controller;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Extension\FieldService;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Extension\TwigLoaderService;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Action;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Actions;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Field;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Fields;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Filter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Filters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\String\Inflector\EnglishInflector;
use Symfony\Component\String\Inflector\InflectorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Creates a Crud for a given entity managed by Doctrine.
 * @template T
 */
abstract class Crud extends AbstractController
{

    private const ITEMS_PER_PAGE = 15;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ServiceEntityRepository;
     */
    protected $repository;

    /**
     * @var ClassMetadata
     */
    protected $metadata;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var Fields|Field[]
     */
    protected $fields;

    /**
     * @var Filters|Filter[]
     */
    protected $filters;

    /**
     * @var FieldService
     */
    protected $fieldService;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var InflectorInterface
     */
    protected $inflector;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var TwigLoaderService
     */
    protected $twigLoader;

    /**
     * @var bool If the Crud is active and fully loaded
     */
    protected $isPrimary = false;

    /**
     * @var string
     * @internal
     */
    private $_cachedFetchMode;

    /**
     * @internal
     * Used to set the dependencies.
     * We don't get them through a constructor to make it easier to override it custom dependencies.
     */
    public function setInternalDependencies(EntityManagerInterface $em, FieldService $fieldService, RequestStack $requestStack, EventDispatcherInterface $eventDispatcher, TranslatorInterface $translator, TwigLoaderService $twigLoader, Reader $reader): void
    {
        $this->em = $em;
        $this->repository = $em->getRepository($this->getEntity());
        $this->fieldService = $fieldService;
        $this->request = $requestStack->getCurrentRequest();
        $this->eventDispatcher = $eventDispatcher;
        $this->inflector = new EnglishInflector();
        $this->translator = $translator;
        $this->twigLoader = $twigLoader;
        $this->reader = $reader;
    }

    /**
     * The entity class FQN
     */
    abstract public function getEntity(): string;


    public function getName(): string
    {
        return (new \ReflectionClass($this->getEntity()))->getShortName();
    }

    public function getPluralName(): string
    {
        return $this->inflector->pluralize($this->getName())[0];
    }

    /**
     * The path that will be used for routing
     */
    public function getRoute(): string
    {
        return strtolower((new \ReflectionClass($this->getEntity()))->getShortName());
    }

    /**
     * Global actions (actions that don't apply to a single existing entity, such as "create".
     */
    public function getGlobalActions(): ?Actions
    {
        $res = new Actions();

        if ($this->isCreatable()) {
            $createAction = new Action('create');
            $createAction->setLabel($this->translator->trans('Create') . ' ' . $this->translator->trans($this->getName()));
            $createAction->setIcon('plus');
            $createAction->addClasses('btn', 'btn-primary');
            $res->add($createAction);
        }

        if ($this->isPrimary && !$this->getFilters()->isEmpty()) {
            $filterAction = new Action('filter');
            $filterAction->addClasses('btn', 'btn-white');
            $filterAction->setAttributes(['data-controller' => 'filter--modal', 'data-action' => 'filter--modal#open', 'data-ajax-route' => $this->generateUrl("qag.{$this->getRoute()}_filter_form_ajax")]);
            $filterAction->setIcon('filter');
            $filterAction->setCustomHref('#');
            $res->add($filterAction);
        }

        return $res;
    }

    /**
     * Actions that can be applied to a single existing entity, such as "Edit" or "Delete"
     */
    public function getActions($entity): ?Actions
    {
        $actions = new Actions();

        if ($this->isViewable($entity)) {
            $editAction = new Action('view');
            $editAction->addClasses('btn', 'btn-outline-primary');
            $actions->add($editAction);
        }

        if ($this->isEditable($entity)) {
            $editAction = new Action('edit');
            $editAction->addClasses('btn', 'btn-outline-primary');
            $actions->add($editAction);
        }

        if ($this->isDeletable($entity)) {
            $removeAction = new Action('delete');
            $removeAction->addClasses('btn', 'btn-outline-danger');
            $removeAction->addDropDownClass('text-danger');
            $removeAction->setAttributes(['data-controller' => 'modal-form', 'data-action' => 'modal-form#open', 'data-target' => '#delete-modal']);
            $actions->add($removeAction);
        }

        return $actions;
    }

    /**
     * All the actions available for a list of entities
     */
    public function getActionsPerEntities(iterable $entities): array
    {
        $res = [];
        foreach ($entities as $entity) {
            $res[] = $this->getActions($entity);
        }

        return $res;
    }

    /**
     * The batch actions available for a lit of entities
     */
    public function getBatchActions(iterable $entities): ?Actions
    {
        $actions = new Actions();

        // remove the "Delete" batch action if at least one entity is not deletable
        foreach ($entities as $entity) {
            if (!$this->isDeletable($entity)) {
                return $actions;
            }
        }

        $removeAction = new Action('delete');
        $removeAction->addClasses('btn', 'btn-outline-danger');
        $removeAction->setAttributes(['data-controller' => 'modal-form', 'data-action' => 'modal-form#open', 'data-target' => '#batch-delete-modal']);
        $actions->add($removeAction);

        return $actions;
    }

    /**
     * Removes an entity
     */
    public function deleteAction($entity): Response
    {
        if (!$this->isCsrfTokenValid('delete', $this->request->request->get('token'))) {
            return $this->redirectToList();
        }
        if (!$this->isDeletable($entity)) {
            throw $this->createAccessDeniedException("Entity {$this->getEntity()} is not removable.");
        }
        if ($this->hasQuickListQueryBuilderSecurity() && !$this->entityIsInList($entity)) {
            throw $this->createAccessDeniedException("Entity {$this->getEntity()} #{$entity->getId()} is filtered out.");
        }
        $this->removeEntity($entity);
        $this->em->flush();
        $this->addFlash('success', $this->translator->trans('entity_deleted', ['%entity%' => $entity]));

        return $this->redirectToList();
    }


    /**
     * Removes multiple entities
     */
    public function deleteBatchAction(): Response
    {
        if (!$this->isCsrfTokenValid('batch', $this->request->request->get('token'))) {
            return $this->redirectToList();
        }
        $checked = $this->request->request->get('batch-actions');
        $nbChecked = \count($checked);
        foreach ($checked as $k => $v) {
            /** @var T $entity */
            $entity = $this->repository->find($k);
            if (!$this->isDeletable($entity)) {
                throw $this->createAccessDeniedException("Entity {$this->getEntity()} is not removable.");
            }
            if ($this->hasQuickListQueryBuilderSecurity() && !$this->entityIsInList($entity)) {
                throw $this->createAccessDeniedException("Entity {$this->getEntity()} #{$entity->getId()} is filtered out.");
            }
            $this->removeEntity($entity);
        }
        $this->em->flush();
        if ($nbChecked === 1) {
            $this->addFlash('success', $this->translator->trans('one_entity_deleted', ['%entity_name%' => $this->getName()]));
        } else {
            $this->addFlash('success', $this->translator->trans('multiple_entity_deleted', ['%count%' => $nbChecked, '%entity_name_plural%' => $this->getPluralName()]));
        }

        return $this->redirectToList();
    }

    public function toggleBooleanPostAction(Request $request, $entity): Response
    {
        if (!$this->isEditable($entity)) {
            throw $this->createAccessDeniedException("Entity {$this->getEntity()} cannot be edited.");
        }
        if ($this->hasQuickListQueryBuilderSecurity() && !$this->entityIsInList($entity)) {
            throw $this->createAccessDeniedException("Entity {$this->getEntity()} #{$entity->getId()} is filtered out.");
        }

        $index = $request->request->get('index');
        $value = $request->request->getBoolean('checked');
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        try {
            $propertyAccessor->setValue($entity, $index, $value);
        } catch (\Exception $e) {
            throw $this->createAccessDeniedException("Entity {$this->getEntity()}'s property $index cannot be read or written");
        }

        $this->em->flush();

        return new JsonResponse();
    }

    /**
     * Removes an entity from the entity manager.
     * @param T $entity
     */
    protected function removeEntity($entity): void
    {
        $this->em->remove($entity);
    }

    /**
     * Checks if an entity can be deleted.
     * @param T $entity
     */
    public function isDeletable($entity): bool
    {
        return true;
    }

    /**
     * Checks if an entity can be created.
     */
    public function isCreatable(): bool
    {
        return true;
    }

    /**
     * Checks if an entity can be edited
     * @param T $entity
     */
    public function isEditable($entity): bool
    {
        return true;
    }

    /**
     * Checks if an entity can be shown
     * @param T $entity
     */
    public function isViewable($entity): bool
    {
        return false;
    }

    /**
     * Checks if an entity is searchable
     */
    public function isSearchable(): bool
    {
        return true;
    }

    /**
     * The default number of item per pages.
     */
    protected function getItemsPerPage(): int
    {
        return self::ITEMS_PER_PAGE;
    }

    /**
     * Lists all the entities and actions.
     * Can be filtered with the ListQueryBuilder method
     */
    public function listAction(Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $this->getListQueryBuilder();

        $isSearchable = $this->isSearchable();
        $search = null;
        if ($isSearchable) {
            $search = $request->query->get('search');
            if ($search !== null) {
                $this->search($queryBuilder, $search);
            }
        }

        $filters = $request->query->get('filter');
        $filterForm = null;
        $activeFiltersNb = 0;
        if ($filters) {
            $filterForm = $this->createFilterForm()->getForm();
            $filterForm->handleRequest($request);
            foreach ($this->getFilters() as $f) {
                /** @var Filter $f */
                if (!$f->getFilterForm()->isEmpty($filters[$f->getIndex()])) {
                    $f->getFilterForm()->addToQueryBuilder($queryBuilder, $filterForm, $f);
                    $activeFiltersNb++;
                }
            }
        }

        $fields = $this->getListingFields();
        $paginationOptions = $this->getPaginationOptions($fields);
        $entities = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            $this->getItemsPerPage(),
            $paginationOptions
        );

        $actionsEntities = $this->getActionsPerEntities($entities);

        return $this->render($this->listTwig(), [
            'route' => 'qag.' . $this->getRoute(),
            'global_actions' => $this->getGlobalActions(),
            'actions_entities' => $actionsEntities,
            'batch_actions' => $this->getBatchActions($entities),
            'entities' => $entities,
            'fields' => $fields,
            'name' => $this->getName(),
            'plural_name' => $this->getPluralName(),
            'description' => $this->getDescription(),
            'search' => $search,
            'is_searchable' => $isSearchable,
            'has_filters' => !$this->getFilters()->isEmpty(),
            'active_filters_nb' => $activeFiltersNb,
            'has_active_filters' => $activeFiltersNb > 0,
            'filter_form' => $filterForm ? $filterForm->createView() : null,
            'filter_form_twig' => $this->filterFormTwig(),
            'is_simple_responsive_mode' => $this->simpleResponsiveMode(),
            'has_actions' => $this->hasActions($actionsEntities),
        ]);
    }

    /**
     * Quick search through a string
     */
    public function search(QueryBuilder $queryBuilder, string $search): void
    {
        $fields = $this->metadata->getFieldNames();

        $query = '';
        foreach ($fields as $field) {
            if ($query !== '') {
                $query .= ' or ';
            }
            $query .= "e.$field LIKE :search";
        }

        if ($query !== '') {
            $queryBuilder->andWhere($query);
            $queryBuilder->setParameter('search', "%$search%");
        }
    }

    /**
     * Filters a list of entity through the query builder.
     * Can also be called to check if the entity was filtered out for quick security (if $this->hasQuickListQueryBuilderSecurity() returns true)
     */
    public function getListQueryBuilder(): QueryBuilder
    {
        $associations = $this->metadata->getAssociationNames();

        $queryBuilder = $this->repository->createQueryBuilder('e');
        foreach ($associations as $association) {
            $queryBuilder->leftJoin("e.$association", $association);
        }

        return $queryBuilder;
    }

    /**
     * View an entity.
     * @param T $entity
     */
    public function viewAction(Request $request, $entity): Response
    {
        if (!$this->isViewable($entity)) {
            throw $this->createAccessDeniedException("Entity {$this->getEntity()} cannot be viewed.");
        }
        if ($this->hasQuickListQueryBuilderSecurity() && !$this->entityIsInList($entity)) {
            throw $this->createAccessDeniedException("Entity {$this->getEntity()} #{$entity->getId()} is filtered out.");
        }
        if ($entity === null) {
            throw $this->createNotFoundException("No {$this->getName()} found with id #{$request->attributes->get('id')}");
        }
        $request->attributes->add(['qag.from' => 'view']);

        return $this->render($this->viewTwig(), [
            'name' => $this->getName(),
            'plural_name' => $this->getPluralName(),
            'action_name' => 'View',
            'list' => $this->backUrl(),
            'back' => $this->backUrl(),
            'fields' => $this->getListingFields(),
            'entity' => $entity,
            'actions' => $this->getActions($entity)
        ]);
    }

    /**
     * Create a new entity
     */
    public function createAction(Request $request): Response
    {
        if (!$this->isCreatable()) {
            throw $this->createAccessDeniedException("Entity {$this->getEntity()} cannot be created.");
        }

        $entity = $this->createNew();

        $form = $this->getForm($entity, true);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $event = new GenericEvent($entity);
            $this->eventDispatcher->dispatch($event, 'qag.events.post_create');
            $this->addFlash('highlighted_row_id', $entity->getId());

            return $this->redirectToList();
        }

        return $this->render($this->formTwig(true), [
            'creation' => true,
            'name' => $this->getName(),
            'plural_name' => $this->getPluralName(),
            'form' => $form->createView(),
            'back' => $this->backUrl(),
            'action_name' => 'Create'
        ]);
    }

    /**
     * Edit an entity.
     * @param T $entity
     */
    public function editAction(Request $request, $entity)
    {
        if (!$this->isEditable($entity)) {
            throw $this->createAccessDeniedException("Entity {$this->getEntity()} cannot be edited.");
        }
        if ($this->hasQuickListQueryBuilderSecurity() && !$this->entityIsInList($entity)) {
            throw $this->createAccessDeniedException("Entity {$this->getEntity()} #{$entity->getId()} is filtered out.");
        }

        if ($entity === null) {
            throw $this->createNotFoundException("No {$this->getName()} found with id #{$request->attributes->get('id')}");
        }
        $form = $this->getForm($entity, false);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $event = new GenericEvent($entity);
            $this->eventDispatcher->dispatch($event, 'qag.events.post_edit');
            $this->addFlash('highlighted_row_id', $entity->getId());

            return $this->redirectToList();
        }

        return $this->render($this->formTwig(false), [
            'creation' => false,
            'name' => $this->getName(),
            'plural_name' => $this->getPluralName(),
            'entity' => $entity,
            'form' => $form->createView(),
            'list' => $this->backUrl(),
            'back' => $this->backUrl(),
            'action_name' => 'Edit'
        ]);
    }

    /**
     * Calls the entity's constructor. Override this to add default parameters for the said entity.
     * @return T
     */
    protected function createNew(): object
    {
        $entityClass = $this->getEntity();

        return new $entityClass;
    }

    /**
     * Overrides a form type. By default, forms are created using a custom formBuilder.
     */
    protected function overrideFormType($entity, bool $creation): ?string
    {
        return null;
    }

    /**
     * Creates a form from the entity's Fields
     * @param T $entity
     */
    protected function buildForm($entity, bool $creation): FormBuilderInterface
    {
        $fields = $this->getFormFields();

        $builder = $this->createFormBuilder($entity, [
            'block_name' => $this->getRoute(),
            'data_class' => $this->getEntity()
        ]);
        foreach ($fields as $field) {
            /** @var Field $field */

            // event to override how the default form is built
            $event = new GenericEvent($builder, ['field' => $field, 'entity' => $entity]);
            $this->eventDispatcher->dispatch($event, 'qag.events.form.field');
            if ($event->isPropagationStopped()) {
                continue;
            }

            $options = ['label' => $field->getLabel(), 'required' => $field->isRequired()];
            if ($field->getFormClass() !== null) {
                $options['attr'] = ['class' => $field->getFormClass()];
            }
            if ($field->getPlaceholder() !== null) {
                $options['placeholder'] = $field->getPlaceholder();
            }

            switch ($field->getType()) {
                case 'decimal':
                    $builder->add($field->getIndex(), TextType::class, $options);
                    break;
                case 'date':
                    $builder->add($field->getIndex(), $field->getFormType() ?? DateType::class, array_merge($options, [
                        'widget' => 'single_text',
                    ]));
                    break;
                case 'datetime':
                    $builder->add($field->getIndex(), $field->getFormType() ?? DateTimeType::class, array_merge($options, [
                        'widget' => 'single_text',
                    ]));
                    break;
                case 'relation':
                    $options['attr']['data-controller'] = 'select2';
                    $builder->add($field->getIndex(), $field->getFormType() ?? EntityType::class, array_merge($options, [
                        'class' => $field->getAssociationMapping(),
                        'multiple' => false,
                    ]));
                    break;
                case 'relation_to_many':
                    $options['attr']['data-controller'] = 'select2';
                    $builder->add($field->getIndex(), $field->getFormType() ?? EntityType::class, array_merge($options, [
                        'class' => $field->getAssociationMapping(),
                        'multiple' => true,
                        'by_reference' => false,
                    ]));
                    break;
                default:
                    $builder->add($field->getIndex(), $field->getFormType(), $options);
                    break;
            }

        }

        return $builder;
    }

    /**
     * Determines the form. If the FormType is overridden, uses this one. Otherwise, uses the FormBuilder.
     * @param T $entity
     */
    public function getForm($entity, bool $creation): FormInterface
    {
        $overriddenType = $this->overrideFormType($entity, $creation);
        if ($overriddenType !== null) {
            return $this->createForm($overriddenType, $entity);
        }

        return $this->buildForm($entity, $creation)->getForm();
    }

    /**
     * Returns all the entity's attributes that will be turned into Fields.
     */
    protected function getAllEntityFields(): array
    {
        $res = array_merge($this->metadata->getFieldNames(), $this->metadata->getAssociationNames());

        $fetchMode = $this->getFieldFetchMode();
        if ($fetchMode !== \Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\Crud::FETCH_AUTO) {
            // When fetch mode is not automatic, every fields need to have a "Show" annotation to be fetched.
            foreach ($res as $k => $property) {
                $reflectionProperty = $this->metadata->getReflectionProperty($property);
                $annotations = $this->reader->getPropertyAnnotations($reflectionProperty);
                $ignoreField = true;
                foreach ($annotations as $annotation) {
                    if (strpos(get_class($annotation), 'Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\Show') !== false) {
                        $ignoreField = false;
                        break;
                    }
                }
                if ($ignoreField) {
                    unset($res[$k]);
                }
            }
        }


        return $res;
    }

    /**
     * The Field Fetch mode. Auto by default (all attributes will be turned into fields).
     * Can be set to manual, so all fields will require to be manually added either through annotations or through the getListingFields and getFormFields methods
     */
    protected function getFieldFetchMode(): string
    {
        if ($this->_cachedFetchMode === null) {
            $reflectionClass = $this->metadata->getReflectionClass();
            $crudAnnotation = $this->reader->getClassAnnotation($reflectionClass, \Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\Crud::class);
            if ($crudAnnotation !== null) {
                /** @var \Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\Crud $crudAnnotation */
                $this->_cachedFetchMode = $crudAnnotation->fetchMode;
            } else {
                $this->_cachedFetchMode = \Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\Crud::FETCH_AUTO;
            }
        }

        return $this->_cachedFetchMode;
    }

    /**
     * Fields that will be used to display the list of entities.
     */
    protected function getListingFields(): Fields
    {
        return clone $this->fields->filter(static function (Field $field) {
            return $field->isDisplayedInList();
        });
    }

    /**
     * Fields that will be used to display the detail of an entity.
     */
    protected function getViewFields(): Fields
    {
        return $this->getListingFields();
    }

    /**
     * Fields that will be used to to automatically generate the form in the create / edit actions.
     */
    protected function getFormFields(): Fields
    {
        return clone $this->fields->filter(static function (Field $field) {
            return $field->isDisplayedInForm();
        });
    }

    protected function getFilters(): Filters
    {
        return $this->filters;
    }


    /**
     * Called only when the controller is active.
     * Gets the Class Metadata and creates fields and filters.
     */
    public function load(Request $request): void
    {
        if (!$this->isEnabled()) {
            throw $this->createAccessDeniedException();
        }
        $this->isPrimary = true;
        $this->metadata = $this->em->getClassMetadata($this->getEntity());
        $this->fields = $this->createFieldsFromMetadata();
        $this->filters = $this->createFilters();
        $request->attributes->add(['qag.main_controller_route' => $this->getRoute()]);
        if (!method_exists($this->getEntity(), '__toString')) {
            throw new \RuntimeException("Entity {$this->getEntity()} must implement __toString.");
        }
    }

    /**
     * Creates a Fields object without any field by default
     */
    protected function createFields(): Fields
    {
        return new Fields($this->metadata, $this->fieldService);
    }

    /**
     * Creates a Fields object with default fields
     */
    protected function createFieldsFromMetadata(): Fields
    {
        $fields = new Fields($this->metadata, $this->fieldService);
        foreach ($this->getAllEntityFields() as $fieldIndex) {
            $field = $this->fieldService->createField($this->metadata, $fieldIndex, true, $this->getFieldFetchMode());
            if ($field !== null) {
                $fields->add($field);
            }
        }

        return $fields;
    }

    final protected function createFilters(): Filters
    {
        return new Filters($this->metadata, $this->fieldService);
    }

    /**
     * The icon name that will be used for the menu.
     * Check https://preview.tabler.io/icons.html
     */
    public function getIcon(): ?string
    {
        return null;
    }


    /**
     * Gets the filters form
     */
    public function filterFormAjaxAction(Request $request): Response
    {
        $form = $this->createFilterForm()->getForm();
        $form->handleRequest($request);

        return $this->render($this->filterFormTwig(), [
            'form' => $form->createView()
        ]);
    }

    /**
     * The filters form's theme
     */
    public function filterFormTwig(): string
    {
        return '@ArkounayQuickAdminGenerator/crud/filter_form.html.twig';
    }

    /**
     * The view's twig, used in view actions.
     */
    public function viewTwig(): string
    {
        return $this->twigLoader->guessTwigFilePath($this->getRoute(), 'view');
    }

    /**
     * The form's twig, used in edit and create actions.
     */
    public function formTwig(bool $creation): string
    {
        return $this->twigLoader->guessTwigFilePath($this->getRoute(), 'form');
    }

    /**
     * the list's twig
     */
    public function listTwig(): string
    {
        return $this->twigLoader->guessTwigFilePath($this->getRoute(), 'list');
    }

    /**
     * Builds the filter forms
     */
    protected function createFilterForm(): FormBuilderInterface
    {
        $builder = $this->container->get('form.factory')->createNamedBuilder('filter', FormType::class, null, [
            'method' => 'GET',
            'action' => $this->backUrl(),
            'csrf_protection' => false,
        ]);

        foreach ($this->getFilters() as $filter) {
            /** @var Filter $filter */
            $filter->getFilterForm()->addToFormBuilder($builder, $filter);
        }

        return $builder;
    }

    /**
     * All the actions that will generate Routes.
     * Every functions that ends with "Actions" will be considered as an Action and thus, a new route will be automatically created.
     */
    public function getAllActions(): array
    {
        $res = [];
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if (preg_match('/Action$/', $method) === 1) {
                $res[] = substr($method, 0, -6);
            }
        }

        return $res;
    }

    /**
     * Finds the entity from an id.
     * @return T
     */
    public function guessEntity()
    {
        return $this->repository->find($this->request->attributes->get('id'));
    }

    /**
     * A description that will be displayed in the listing page.
     */
    public function getDescription(): string
    {
        return '';
    }

    /**
     * The default paginations options. Used to add a default sorting on the listing page.
     */
    protected function getPaginationOptions(Fields $fields): array
    {
        /** @var Fields|Field[] $fields */
        foreach ($fields as $field) {
            if ($field->getDefaultSortDirection() !== null) {
                return ['defaultSortFieldName' => 'e.' . $field->getIndex(), 'defaultSortDirection' => $field->getDefaultSortDirection()];
            }
        }
        if ($this->metadata->hasField('position')) {
            return ['defaultSortFieldName' => 'e.position', 'defaultSortDirection' => 'asc'];
        }
        if ($this->metadata->hasField('createdAt')) {
            return ['defaultSortFieldName' => 'e.createdAt', 'defaultSortDirection' => 'desc'];
        }
        if ($this->metadata->hasField('id')) {
            return ['defaultSortFieldName' => 'e.id', 'defaultSortDirection' => 'desc'];
        }

        return [];
    }

    /**
     * True by default
     * If true, the responsive mode will be simplified, there won't be a table but a simple list that will display entity's toString().
     * This removes batch actions and fields informations.
     * Return false to use a responsive table instead.
     */
    protected function simpleResponsiveMode(): bool
    {
        return true;
    }

    /**
     * Return true if the controller can be loaded and displayed in the menu.
     */
    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * Redirects to the list and preserves the referer (with the page number for example)
     */
    protected function redirectToList(): RedirectResponse
    {
        return $this->redirectToRoute('qag.' . $this->getRoute(), $this->getListRouteParams());
    }

    protected function backUrl(): string
    {
        return $this->generateUrl('qag.' . $this->getRoute(), $this->getListRouteParams());
    }

    protected function getListRouteParams(): array
    {
        $params = array_merge($this->request->query->all(), $this->request->get('referer', []));
        unset($params['highlight'], $params['referer']);
        return $params;
    }

    /**
     * Checks if there are actions to display in the page, so the last column can be removed if there are not.
     */
    protected function hasActions(array $actionEntities): bool
    {
        foreach ($actionEntities as $actions) {
            if (!empty($actions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Allows to check if the current entity was filtered out or not through the getListQueryBuilder method, at the cost of one extra request.
     * This can be useful if the CRUD has conditions to display some entities, with multiple roles or user for example.
     * False by default
     */
    protected function hasQuickListQueryBuilderSecurity(): bool
    {
        return false;
    }

    /**
     * Used to check if the entity is a part of the getListQueryBuilder
     */
    protected function entityIsInList($entity): bool
    {
        return $this->getListQueryBuilder()
                ->andWhere('e.id = :id')
                ->setParameter('id', $entity->getId())
                ->getQuery()
                ->getOneOrNullResult() !== null;
    }

}