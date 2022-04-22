<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Extension\FieldService;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @template-extends TypedArray<int, Filter>
 * @method Filter get(string $field)
 */
class Filters extends TypedArray
{

    /**
     * @var FieldService
     */
    private $fieldService;

    /**
     * @var ClassMetadata
     */
    private $metadata;


    public function __construct(ClassMetadata $metadata, FieldService $fieldService)
    {
        $this->fieldService = $fieldService;
        $this->metadata = $metadata;
    }

    protected function createFromIndexName(string $index): Listable
    {
        return $this->fieldService->createFilter($this->metadata, $index);
    }

    protected function getType(): string
    {
        return Filter::class;
    }
}