<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Extension;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\Crud;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\HideInForm;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\HideInList;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\Ignore;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\ShowInForm;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\ShowInList;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\Sort;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Field;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Filter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\BooleanFilter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\DateFilter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\DateTimeFilter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\EntityFilter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\IntegerFilter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\StringFilter;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FieldService
{

    /**
     * @var TwigLoaderService
     */
    private $twigLoader;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var FormRendererInterface
     */
    private $formRenderer;

    public function __construct(TwigLoaderService $twigLoader, EventDispatcherInterface $dispatcher, Reader $reader, FormRenderer $formRenderer)
    {
        $this->twigLoader = $twigLoader;
        $this->dispatcher = $dispatcher;
        $this->reader = $reader;
        $this->formRenderer = $formRenderer;
    }

    public function createField(ClassMetadata $metadata, string $fieldIndex, bool $automatic = false, string $fetchMode = Crud::FETCH_AUTO): ?Field
    {
        $field = new Field($fieldIndex);
        if ($fieldIndex === 'id') {
            $field->setDisplayedInForm(false);
        }

        /** @var \Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\Field $annotationField */
        $annotationField = null;
        if ($metadata) {
            $hasField = $metadata->hasField($fieldIndex);
            if ($hasField || $metadata->hasAssociation($fieldIndex)) {
                $reflectionProperty = $metadata->getReflectionProperty($fieldIndex);

                if ($reflectionProperty) {

                    if ($automatic) {
                        $ignore = $this->getAttribute($reflectionProperty, Ignore::class);
                        if ($ignore !== null) {
                            return null;
                        }
                        $hideInForm = $this->getAttribute($reflectionProperty, HideInForm::class);
                        if ($hideInForm !== null) {
                            $field->setDisplayedInForm(false);
                        }
                        $hideInList = $this->getAttribute($reflectionProperty, HideInList::class);
                        if ($hideInList !== null) {
                            $field->setDisplayedInList(false);
                        }
                    }
                    if ($fetchMode === Crud::FETCH_MANUAL) {

                        // handle show annotations for manual fetch mode
                        $showInForm = $this->getAttribute($reflectionProperty, ShowInForm::class);
                        $showInList = $this->getAttribute($reflectionProperty, ShowInList::class);
                        if ($showInForm === null && $showInList !== null) {
                            $field->setDisplayedInForm(false);
                        } elseif ($showInForm !== null && $showInList === null) {
                            $field->setDisplayedInList(false);
                        }
                    }
                    /** @var Sort $sort */
                    $sort = $this->getAttribute($reflectionProperty, Sort::class);
                    if ($sort !== null) {
                        $field->setDefaultSortDirection($sort->direction);
                    }
                    $annotationField = $this->getAttribute($reflectionProperty, \Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\Field::class);
                }

                if ($hasField) {
                    $fieldMapping = $metadata->getFieldMapping($fieldIndex);
                    $nullable = $fieldMapping['nullable'] ?? false;
                    if ($fieldMapping['type'] === 'boolean') {
                        $nullable = true;
                    }
                    $field->setRequired(!$nullable);
                }
            }
        }

        $field->setLabel($annotationField !== null && $annotationField->label ? $annotationField->label : $this->formRenderer->humanize($fieldIndex));
        $field->setType($metadata ? $this->getType($metadata, $fieldIndex) : 'virtual');
        $field->setTwig($this->twigLoader->getTwigPartialByFieldType($field->getType(), $annotationField !== null ? $annotationField->twigName : null));

        switch ($field->getType()) {
            case 'virtual':
            case 'relation_to_many':
                $field->setSortable(false);
                if ($metadata && $metadata->hasAssociation($fieldIndex)) {
                    $field->setAssociationMapping($metadata->getAssociationMapping($fieldIndex)['targetEntity']);
                }
                break;
            case 'relation':
                if ($metadata && $metadata->hasAssociation($fieldIndex)) {
                    $associationMapping = $metadata->getAssociationMapping($fieldIndex);
                    $field->setAssociationMapping($associationMapping['targetEntity']);
                    $nullable = $associationMapping['joinColumns'][0]['nullable'] ?? true;
                    $field->setRequired(!$nullable);
                }
                $field->setSortable(true);
                $field->setSortQuery("{$field->getIndex()}.id");
                break;
            default:
                $field->setSortable(true);
                $field->setSortQuery("e.{$field->getIndex()}");
                break;
        }

        if ($annotationField !== null) {
            if ($annotationField->required !== null) {
                $field->setRequired($annotationField->required);
            }
            if ($annotationField->sortable !== null) {
                $field->setSortable($annotationField->sortable);
            }
            $field->setFormClass($annotationField->formClass);
            $field->setFormType($annotationField->formType);
            $field->setPlaceholder($annotationField->placeholder);
            $field->setHelp($annotationField->help);
            $field->setPosition($annotationField->position);
            $field->setPayload($annotationField->payload);
        }

        $event = new GenericEvent($field, ['metadata' => $metadata]);
        $this->dispatcher->dispatch($event, 'qag.events.field_generation');

        return $field;
    }

    public function createFilter(ClassMetadata $metadata, string $filterIndex): Filter
    {
        $filterForm = null;
        $metadataType = $this->getType($metadata, $filterIndex);
        switch ($metadataType) {
            case 'virtual':
                throw new \RuntimeException('Filters are not supported for virtual fields');
            case 'date':
                $filterForm = new DateFilter();
                break;
            case 'datetime':
                $filterForm = new DateTimeFilter();
                break;
            case 'string':
                $filterForm = new StringFilter();
                break;
            case 'boolean':
                $filterForm = new BooleanFilter();
                break;
            case 'integer':
                $filterForm = new IntegerFilter();
                break;
            case 'relation':
                $filterForm = new EntityFilter($metadata->getAssociationTargetClass($filterIndex));
                break;
        }

        if ($filterForm === null) {
            throw new \RuntimeException('Filter not supported for type "'.$metadataType.'". Specify filterType manually.');
        }

        return new Filter($filterIndex, $filterForm);
    }


    protected function getType(ClassMetadata $metadata, string $fieldIndex): string
    {
        if (isset($metadata->fieldMappings[$fieldIndex])) {
            $type = $metadata->getFieldMapping($fieldIndex)['type'];
        } elseif (isset($metadata->associationMappings[$fieldIndex])) {
            $type = 'relation';
            $associationMapping = $metadata->getAssociationMapping($fieldIndex);
            if ($associationMapping['type'] === ClassMetadata::MANY_TO_MANY || $associationMapping['type'] === ClassMetadata::ONE_TO_MANY) {
                $type .= '_to_many';
            }
        } else {
            $type = 'virtual';
        }

        return $type;
    }

    protected function getAttribute(\ReflectionProperty $reflectionProperty, $class)
    {
        $res = $this->reader->getPropertyAnnotation($reflectionProperty, $class);
        if ($res === null) {
            $attributes = $reflectionProperty->getAttributes($class);
            if (!empty($attributes)) {
                $res = $attributes[0]->newInstance();
            }
        }

        return $res;
    }

}
