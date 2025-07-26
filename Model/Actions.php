<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model;

use function Symfony\Component\String\u;

/**
 * @template TKey of array-key
 * @template-extends TypedArray<TKey, Action>
 * @method Action get(string $field)
 */
class Actions extends TypedArray
{

    public function createFromIndexName(string $index): Listable
    {
        $action = new Action($index);
        $action->addClasses('action-' . $index, 'btn');
        $action->setLabel(u($index)->title()->toString());

        return $action;
    }

    protected function getType(): string
    {
        return Action::class;
    }
}