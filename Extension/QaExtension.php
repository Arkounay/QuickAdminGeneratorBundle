<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Extension;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class QaExtension extends AbstractExtension implements GlobalsInterface
{

    /**
     * @var Crud[]
     */
    private $cruds;

    /**
     * @var array
     */
    private $config;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(iterable $cruds, array $config, TranslatorInterface $translator)
    {

        $this->cruds = $cruds;
        $this->config = $config;
        $this->translator = $translator;
    }

    private function getMenuItems():array
    {
        $res = [];
        if (!isset($this->config['menu']['items'])) {
            foreach ($this->cruds as $crud) {
                /** @var Crud $crud */
                $res[$this->translator->trans($crud->getPluralName())] = $crud;
            }
            ksort($res);
        } elseif (is_iterable($this->config['menu']['items'])) {
            $items = $this->getCrudAsKArrayWithClassKeys($this->cruds);
            $menuItems = [];
            foreach ($this->config['menu']['items'] as $class) {
                $menuItems[] = $items[$class];
            }
            $res = $menuItems;
        } elseif (class_exists($this->config['menu']['items'])) {
            $items = $this->getCrudAsKArrayWithClassKeys($this->cruds);
            $res = (new $this->config['menu']['items'])($items);
        } else {
            throw new \InvalidArgumentException('Could not generate menu. Please check in the yaml arkounay_quick_admin_generator.menu.items is correct.');
        }

        return $res;
    }

    /**
     * Converts a Cruds iterable to an array of crud with their class name as keys
     * @return Crud[]
     */
    private function getCrudAsKArrayWithClassKeys(iterable $cruds): array
    {
        $items = [];
        foreach ($cruds as $crud) {
            $items[get_class($crud)] = $crud;
        }

        return $items;
    }

    public function getGlobals(): array
    {
        return ['qag' => ['menu_items' => $this->getMenuItems(), 'config' => $this->config]];
    }

}
