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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Inflector\EnglishInflector;
use Symfony\Component\String\Inflector\InflectorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class Crud extends AbstractController
{

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
    private $fieldService;

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
    private $translator;

    /**
     * @var TwigLoaderService
     */
    private $twigLoader;

    /**
     * @var bool If the Crud is active and fully loaded
     */
    protected $isPrimary = false;

    /**
     * @internal
     * Used to set the dependencies.
     * We don't get them through a constructor to make it easier to override it custom dependencies.
     */
    public function setInternalDependencies(EntityManagerInterface $em, FieldService $fieldService, RequestStack $requestStack, EventDispatcherInterface $eventDispatcher, TranslatorInterface $translator, TwigLoaderService $twigLoader): void
    {
        $this->em = $em;
        $this->repository = $em->getRepository($this->getEntity());
        $this->fieldService = $fieldService;
        $this->request = $requestStack->getCurrentRequest();
        $this->eventDispatcher = $eventDispatcher;
        $this->inflector = new EnglishInflector();
        $this->translator = $translator;
        $this->twigLoader = $twigLoader;
    }

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
     * @return string The name that will be used for routing
     */
    public function getRoute(): string
    {
        return strtolower((new \ReflectionClass($this->getEntity()))->getShortName());
    }

    public function getGlobalActions(): ?Actions
    {
        $res = new Actions();

        if ($this->isCreatable()) {
            $createAction = new Action('create');
            $createAction->setIcon('plus');
            $createAction->addClasses('btn', 'btn-primary');
            $res->add($createAction);
        }

        if ($this->isPrimary && !$this->getFilters()->isEmpty()) {
            $filterAction = new Action('filter');
            $filterAction->addClasses('btn', 'btn-white', 'js-filter');
            $filterAction->setIcon('filter');
            $filterAction->setCustomHref('#');
            $res->add($filterAction);
        }

        return $res;
    }

    public function getActions($entity): ?Actions
    {
        $actions = new Actions();

        if ($this->isEditable($entity)) {
            $editAction = new Action('edit');
            $editAction->addClasses('btn', 'btn-outline-primary');
            $actions->add($editAction);
        }

        if ($this->isDeletable($entity)) {
            $removeAction = new Action('delete');
            $removeAction->addClasses('btn', 'btn-outline-danger');
            $removeAction->addDropDownClass('text-danger');
            $removeAction->addSharedClasses('js-delete-item');
            $actions->add($removeAction);
        }

        return $actions;
    }

    public function getActionsPerEntities($entities): array
    {
        $res = [];
        foreach ($entities as $entity) {
            $res[] = $this->getActions($entity);
        }

        return $res;
    }

    public function getBatchActions($entities): ?Actions
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
        $removeAction->addSharedClasses('js-delete-items');
        $actions->add($removeAction);

        return $actions;
    }

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

    public function deleteBatchAction(): Response
    {
        if (!$this->isCsrfTokenValid('batch', $this->request->request->get('token'))) {
            return $this->redirectToList();
        }
        $checked = $this->request->request->get('batch-actions');
        $nbChecked = \count($checked);
        foreach ($checked as $k => $v) {
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

    protected function removeEntity($entity): void
    {
        $this->em->remove($entity);
    }

    /**
     * @param $entity mixed can be null (for batch actions)
     */
    public function isDeletable($entity): bool
    {
        return true;
    }

    public function isCreatable(): bool
    {
        return true;
    }

    public function isEditable($entity): bool
    {
        return true;
    }

    public function isSearchable(): bool
    {
        return true;
    }

    protected function getItemsPerPage(): int
    {
        return 13;
    }

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

        return $this->render('@ArkounayQuickAdminGenerator/crud/list.html.twig', [
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

    protected function search(QueryBuilder $queryBuilder, string $search): void
    {
        $fields = $this->metadata->getFieldNames();

        foreach ($fields as $field) {
            $queryBuilder->orWhere("e.$field LIKE :search");
        }
        $queryBuilder->setParameter('search', "%$search%");
    }

    protected function getListQueryBuilder(): QueryBuilder
    {
        $associations = $this->metadata->getAssociationNames();

        $queryBuilder = $this->repository->createQueryBuilder('e');
        foreach ($associations as $association) {
            $queryBuilder->leftJoin("e.$association", $association);
        }

        return $queryBuilder;
    }

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

            return $this->redirectToList();
        }

        return $this->render($this->formTwig(), [
            'creation' => true,
            'name' => $this->getName(),
            'plural_name' => $this->getPluralName(),
            'form' => $form->createView(),
            'back' => $this->generateUrl('qag.' . $this->getRoute())
        ]);
    }

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

            return $this->redirectToList();
        }

        return $this->render($this->formTwig(), [
            'creation' => false,
            'name' => $this->getName(),
            'plural_name' => $this->getPluralName(),
            'form' => $form->createView(),
            'back' => $this->generateUrl('qag.' . $this->getRoute(), $request->get('referer', []))
        ]);
    }

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

            switch ($field->getType()) {
                case 'decimal':
                    $builder->add($field->getIndex(), TextType::class, $options);
                    break;
                case 'date':
                    $builder->add($field->getIndex(), $field->getFormType() ?? DateTimeType::class, array_merge($options, [
                        'widget' => 'single_text',
                    ]));
                    break;
                case 'datetime':
                    $builder->add($field->getIndex(), $field->getFormType() ?? DateTimeType::class, array_merge($options, [
                        'widget' => 'single_text',
                    ]));
                    break;
                case 'relation_to_many':
                    $builder->add($field->getIndex(), $field->getFormType() ?? EntityType::class, array_merge($options, [
                        'class' => $field->getAssociationMapping(),
                        'multiple' => true,
                        'required' => false,
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
     */
    public function getForm($entity, bool $creation): FormInterface
    {
        $overriddenType = $this->overrideFormType($entity, $creation);
        if ($overriddenType !== null) {
            return $this->createForm($overriddenType, $entity);
        }

        return $this->buildForm($entity, $creation)->getForm();
    }

    protected function getAllEntityFields(): array
    {
        return array_merge($this->metadata->getFieldNames(), $this->metadata->getAssociationNames());
    }

    protected function getListingFields(): Fields
    {
        return clone $this->fields->filter(static function (Field $field) {
            return $field->isDisplayedInList();
        });
    }

    protected function getFilters(): Filters
    {
        return $this->filters;
    }

    protected function getFormFields(): Fields
    {
        return clone $this->fields->filter(static function (Field $field) {
            return $field->isDisplayedInEdition();
        });
    }

    /**
     * Called only when the controller is active
     */
    public function load(): void
    {
        if (!$this->isEnabled()) {
            throw $this->createAccessDeniedException();
        }
        $this->isPrimary = true;
        $this->metadata = $this->em->getClassMetadata($this->getEntity());
        $this->fields = $this->createFieldsFromMetadata();
        $this->filters = $this->createFilters();
    }

    protected function createFields(): Fields
    {
        return new Fields($this->metadata, $this->fieldService);
    }

    protected function createFieldsFromMetadata(): Fields
    {
        $fields = new Fields($this->metadata, $this->fieldService);
        foreach ($this->getAllEntityFields() as $fieldIndex) {
            $field = $this->fieldService->createField($this->metadata, $fieldIndex);
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

    public function getIcon(): ?string
    {
        return null;
    }

    public function filterFormAjaxAction(Request $request): Response
    {
        $form = $this->createFilterForm()->getForm();
        $form->handleRequest($request);

        return $this->render($this->filterFormTwig(), [
            'form' => $form->createView()
        ]);
    }

    public function filterFormTwig(): string
    {
        return '@ArkounayQuickAdminGenerator/crud/filter_form.html.twig';
    }

    public function formTwig(): string
    {
        return $this->twigLoader->getTwigFormType($this->getRoute(), 'form');
    }

    public function listTwig(): string
    {
        return $this->twigLoader->getTwigFormType($this->getRoute(), 'list');
    }

    protected function createFilterForm(): FormBuilderInterface
    {
        $builder = $this->container->get('form.factory')->createNamedBuilder('filter', FormType::class, null, [
            'method' => 'GET',
            'action' => $this->generateUrl('qag.' . $this->getRoute()),
            'csrf_protection' => false,
        ]);

        foreach ($this->getFilters() as $filter) {
            /** @var Filter $filter */
            $filter->getFilterForm()->addToFormBuilder($builder, $filter);
        }

        return $builder;
    }

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

    public function guessEntity()
    {
        return $this->repository->find($this->request->attributes->get('id'));
    }

    public function getDescription(): string
    {
        return '';
    }

    protected function getPaginationOptions(Fields $fields): array
    {
        /** @var Fields|Field[] $fields */
        foreach ($fields as $field) {
            if ($field->getDefaultSortDirection() !== null) {
                return ['defaultSortFieldName' => 'e.' . $field->getIndex(), 'defaultSortDirection' => $field->getDefaultSortDirection()];
            }
        }
        if (isset($fields['position'])) {
            return ['defaultSortFieldName' => 'e.position', 'defaultSortDirection' => 'asc'];
        }
        if (isset($fields['id'])) {
            return ['defaultSortFieldName' => 'e.id', 'defaultSortDirection' => 'desc'];
        }

        return [];
    }

    protected function simpleResponsiveMode(): bool
    {
        return true;
    }

    public function isEnabled(): bool
    {
        return true;
    }

    protected function redirectToList(): RedirectResponse
    {
        return $this->redirectToRoute('qag.' . $this->getRoute(), $this->request->get('referer', []));
    }

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
     * @return bool
     */
    protected function hasQuickListQueryBuilderSecurity(): bool
    {
        return false;
    }

    protected function entityIsInList($entity): bool
    {
        return $this->getListQueryBuilder()
            ->andWhere('e.id = :id')
            ->setParameter('id', $entity->getId())
            ->getQuery()
            ->getOneOrNullResult() !== null;
    }

}