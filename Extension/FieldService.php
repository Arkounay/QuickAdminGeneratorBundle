<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Extension;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\HideInEdition;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\HideInList;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\Ignore;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\ShowInEdition;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\ShowInList;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\Sort;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Field;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Filter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\DateFilter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\DateTimeFilter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\EntityFilter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\IntegerFilter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\StringFilter;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function Symfony\Component\String\u;
use Symfony\Component\Form\FormRenderer;

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

    public function __construct(TwigLoaderService $twigLoader, EventDispatcherInterface $dispatcher, Reader $reader)
    {
        $this->twigLoader = $twigLoader;
        $this->dispatcher = $dispatcher;
        $this->reader = $reader;
    }

    public function createField(ClassMetadata $metadata, string $fieldIndex, bool $automatic = false): ?Field
    {
        $field = new Field($fieldIndex);
        if ($fieldIndex === 'id') {
            $field->setDisplayedInEdition(false);
        }

        /** @var \Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\Field $annotationField */
        $annotationField = null;
        if ($metadata) {
            $hasField = $metadata->hasField($fieldIndex);
            if ($hasField || $metadata->hasAssociation($fieldIndex)) {
                $reflectionProperty = $metadata->getReflectionProperty($fieldIndex);

                if ($reflectionProperty) {

                    if ($automatic) {
                        $ignore = $this->reader->getPropertyAnnotation($reflectionProperty, Ignore::class);
                        if ($ignore !== null) {
                            return null;
                        }
                        $hideInEdition = $this->reader->getPropertyAnnotation($reflectionProperty, HideInEdition::class);
                        if ($hideInEdition !== null) {
                            $field->setDisplayedInEdition(false);
                        }
                        $hideInList = $this->reader->getPropertyAnnotation($reflectionProperty, HideInList::class);
                        if ($hideInList !== null) {
                            $field->setDisplayedInList(false);
                        }

                        // handle show annotations for manual fetch mode
                        $showInEdition = $this->reader->getPropertyAnnotation($reflectionProperty, ShowInEdition::class);
                        $showInList = $this->reader->getPropertyAnnotation($reflectionProperty, ShowInList::class);
                        if ($showInEdition === null && $showInList !== null) {
                            $field->setDisplayedInEdition(false);
                        } elseif ($showInEdition !== null && $showInList === null) {
                            $field->setDisplayedInList(false);
                        }
                    }
                    /** @var Sort $sort */
                    $sort = $this->reader->getPropertyAnnotation($reflectionProperty, Sort::class);
                    if ($sort !== null) {
                        $field->setDefaultSortDirection($sort->direction);
                    }
                    $annotationField = $this->reader->getPropertyAnnotation($reflectionProperty, \Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation\Field::class);
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

        $field->setLabel($annotationField !== null ? $annotationField->label : [FormRenderer::class, 'humanize']($fieldIndex));
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
                    $field->setAssociationMapping($metadata->getAssociationMapping($fieldIndex)['targetEntity']);
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

}
