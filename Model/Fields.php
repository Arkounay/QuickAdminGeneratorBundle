<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Extension\FieldService;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * @template-extends TypedArray<int, Field>
 * @method Field get(string $field)
 */
class Fields extends TypedArray
{

    public function __construct(private ClassMetadata $metadata, private FieldService $fieldService){}

    public function createFromIndexName(string $index): Listable
    {
        return $this->fieldService->createField($this->metadata, $index);
    }

    protected function getType(): string
    {
        return Field::class;
    }

    public function sortByPosition(): self
    {
        uasort($this->items, static fn(Field $a, Field $b): int => $a->getPosition() <=> $b->getPosition());

        return $this;
    }

}