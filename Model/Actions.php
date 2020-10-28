<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Extension\FieldService;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use function Symfony\Component\String\u;

/**
 * @method Action get(string $field)
 */
class Actions extends TypedArray
{

    protected function createFromIndexName(string $index): Listable
    {
        $action = new Action($index);
        $action->addClasses('action-' . $index, 'btn', 'btn-outline-secondary');
        $action->setLabel(u($index)->title()->toString());

        return $action;
    }

    protected function getType(): string
    {
        return Action::class;
    }
}