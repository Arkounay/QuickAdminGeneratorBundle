<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Extension\FieldService;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @template TEntity of object
 * @template-extends TypedArray<int, Filter>
 * @method Filter get(string $field)
 */
class Filters extends TypedArray
{

    /**
     * @param ClassMetadata<TEntity> $metadata
     */
    public function __construct(private readonly ClassMetadata $metadata, private readonly FieldService $fieldService) {}

    public function createFromIndexName(string $index): Listable
    {
        return $this->fieldService->createFilter($this->metadata, $index);
    }

    protected function getType(): string
    {
        return Filter::class;
    }
}