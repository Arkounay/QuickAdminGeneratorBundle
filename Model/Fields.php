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

    public function __construct(private readonly ClassMetadata $metadata, private readonly FieldService $fieldService){}

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


    public function moveToLastPosition(string $index): self
    {
        $maxPosition = 0;
        foreach ($this as $field) {
            if ($field->getPosition() > $maxPosition) {
                $maxPosition = $field->getPosition();
            }
        }
        $this->items[$index]->setPosition($maxPosition + 1);
        return $this;
    }

    public function moveToFirstPosition(string $index): self
    {
        $minPosition = 0;
        foreach ($this as $field) {
            if ($field->getPosition() < $minPosition) {
                $minPosition = $field->getPosition();
            }
        }
        $this->items[$index]->setPosition($minPosition - 1);
        return $this;
    }

}