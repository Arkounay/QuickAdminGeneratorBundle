<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Extension;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Action;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

class QagExtension extends AbstractExtension implements GlobalsInterface
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

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(iterable $cruds, array $config, TranslatorInterface $translator, RouterInterface $router, RequestStack $requestStack)
    {

        $this->cruds = $cruds;
        $this->config = $config;
        $this->translator = $translator;
        $this->router = $router;
        $this->requestStack = $requestStack;
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

    public function getActionHref(Action $action, $entity = null): string
    {
        if ($action->getCustomHref() !== null) {
            return $action->getCustomHref();
        }

        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->attributes->get('qag.main_controller_route');

        try {
            $params = $_GET; /* use $_GET instead of $request->query->all() to avoid knp pagination's change of query attributes */
            unset($params['referer']);
            if ($entity !== null) {
                $params['id'] = $entity->getId();
                if ($request->attributes->get('qag.from') === 'view') {
                    $params['from'] = 'view';
                }
            }
            return $this->router->generate("qag.{$route}_{$action->getIndex()}", $params);
        } catch (\Exception $ignored) {}

        return '#';
    }

    public function getFunctions(): array
    {
        return [new TwigFunction('action_href', [$this, 'getActionHref'])];
    }

    public function getGlobals(): array
    {
        return ['qag' => ['menu_items' => $this->getMenuItems(), 'config' => $this->config]];
    }

}
