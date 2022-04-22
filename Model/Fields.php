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
        return $this->fieldService->createField($this->metadata, $index);
    }

    protected function getType(): string
    {
        return Field::class;
    }

    public function sortByPosition(): self
    {
        uasort($this->items, static function (Field $a, Field $b): int {
            return $a->getPosition() <=> $b->getPosition();
        });

        return $this;
    }


}