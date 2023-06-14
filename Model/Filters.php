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

    public function __construct(private ClassMetadata $metadata, private FieldService $fieldService) {}

    public function createFromIndexName(string $index): Listable
    {
        return $this->fieldService->createFilter($this->metadata, $index);
    }

    protected function getType(): string
    {
        return Filter::class;
    }
}