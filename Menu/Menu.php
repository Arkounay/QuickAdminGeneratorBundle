<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Menu;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Menu implements MenuInterface
{

    /**
     * @var iterable|Crud[]
     */
    protected $cruds;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(iterable $cruds, RequestStack $requestStack, RouterInterface $router, TranslatorInterface $translator, array $config)
    {
        $this->cruds = $cruds;
        $this->requestStack = $requestStack;
        $this->router = $router;
        $this->config = $config;
        $this->translator = $translator;
    }

    /**
     * @return MenuItem[]
     */
    public function generateMenu(): array
    {
        $menuItems = [];
        $request = $this->requestStack->getCurrentRequest();
        if ($request === null) {
            return $menuItems;
        }

        $dashboard = $this->createDashboardMenuItem($request);
        if ($dashboard !== null) {
            $menuItems[] = $dashboard;
        }

        if (!isset($this->config['menu']['items'])) {
            // Generate menu from all the cruds if nothing is specified in yaml, alphabetically
            $cruds = [];
            foreach ($this->cruds as $crud) {
                /** @var Crud $crud */
                if ($crud->isEnabled()) {
                    $cruds[$this->translator->trans($crud->getPluralName())] = $crud;
                }
            }
            ksort($cruds);
            $items = [];
            foreach ($cruds as $crud) {
                $items[$crud::class] = $crud;
            }
            foreach ($items as $class => $crud) {
                $menuItem = $this->createMenuItem($items, $class, $request);
                if ($menuItem !== null) {
                    $menuItems[] = $menuItem;
                }
            }
        } elseif (is_iterable($this->config['menu']['items'])) {
            // Create menu from yaml
            $items = $this->getCrudAsKArrayWithClassKeys($this->cruds);
            foreach ($this->config['menu']['items'] as $item) {
                $menuItem = $this->createMenuItem($items, $item, $request);
                if ($menuItem !== null) {
                    $menuItems[] = $menuItem;
                }
            }
        }

        return $menuItems;
    }

    protected function createDashboardMenuItem(Request $request): ?MenuItem
    {
        if ($this->config['dashboard_route_redirection'] === null) {
            $dashboard = new MenuItem('Dashboard');
            $dashboard->setUrl($this->router->generate('qag.dashboard'));
            $dashboard->setActive($request->attributes->get('_route') === 'qag.dashboard');
            return $dashboard;
        }

        return null;
    }

    /**
     * @param array $cruds available cruds controller
     * @param $node string|array the yaml item. Can be a crud class or an array
     * @return MenuItem|null null if nothing to add for this specific node
     */
    protected function createMenuItem(array $cruds, $node, Request $request): ?MenuItem
    {
        if (is_string($node)) {
            if ($node === 'divider') {
                return new MenuItem('__divider__');
            }
            if (!isset($cruds[$node])) {
                throw new InvalidConfigurationException("'$node' not found in Crud list");
            }
            $crud = $cruds[$node];
            $menuItem = $this->createMenuItemFromCrud($crud, $request);
        } else {
            if (!isset($node['label']) && !isset($node['crud'])) {
                throw new InvalidConfigurationException('Menu item needs to be a Crud class or have a label key');
            }
            if (isset($node['crud'])) {
                $crud = $cruds[$node['crud']];
                $menuItem = $this->createMenuItemFromCrud($crud, $request);
                if ($menuItem === null) {
                    return null;
                }
                if (isset($node['label'])) {
                    $menuItem->setLabel($node['label']);
                }
            } else {
                $menuItem = new MenuItem($node['label']);
            }
            if (isset($node['url'], $node['route'])) {
                throw new InvalidConfigurationException('Menu item cannot have both url and route parameters');
            }
            if (isset($node['url'])) {
                $menuItem->setUrl($node['url']);
            }
            if (isset($node['route'])) {
                $routeParams = $node['route_params'] ?? [];
                $menuItem->setUrl($this->router->generate($node['route'], $routeParams));
                if (str_contains($this->requestStack->getCurrentRequest()?->get('_route'), $node['route'])) {
                    $menuItem->setActive(true);
                }
            }
            if (isset($node['icon'])) {
                $menuItem->setIcon($node['icon']);
            }
            if (isset($node['badge_number'])) {
                $menuItem->setBadgeNumber($node['badge_number']);
            }
            if (isset($node['badge_class'])) {
                $menuItem->setBadgeClass($node['badge_class']);
            }
            if (isset($node['attr'])) {
                foreach ($node['attr'] as $k => $v) {
                    $menuItem->addAttribute($k, $v);
                }
            }
            if (isset($node['children'])) {
                $children = [];
                foreach ($node['children'] as $childNode) {
                    $child = $this->createMenuItem($cruds, $childNode, $request);
                    if ($child === null) {
                        continue;
                    }
                    if ($child->isActive()) {
                        $menuItem->setActive(true);
                    }
                    $children[] = $child;
                }
                $menuItem->setChildren($children);
            }
        }

        return $menuItem;
    }

    protected function createMenuItemFromCrud(Crud $crud, Request $request): ?MenuItem
    {
        if (!$crud->isEnabled()) {
            return null;
        }
        $route = $this->router->generate('qag.' . $crud->getRoute());
        $menuItem = new MenuItem($crud->getPluralName());
        $menuItem->setUrl($route);
        if ($crud->getIcon() !== null) {
            $menuItem->setIcon($crud->getIcon());
        }
        if ($crud->getBadgeNumber() !== null) {
            $menuItem->setBadgeNumber($crud->getBadgeNumber());
        }
        if ($crud->getBadgeClass() !== null) {
            $menuItem->setBadgeClass($crud->getBadgeClass());
        }
        if ($request->attributes->get('_route') !== 'qag.dashboard' && str_contains($request->getPathInfo(), $route)) {
            $menuItem->setActive($route);
        }
        return $menuItem;
    }


    /**
     * Converts a Cruds iterable to an array of crud with their class name as keys
     * @return Crud[]
     */
    private function getCrudAsKArrayWithClassKeys(iterable $cruds): array
    {
        $items = [];
        foreach ($cruds as $crud) {
            if (is_array($crud)) {
                foreach ($crud['children'] as $child) {
                    $items[$child::class] = $child;
                }
            } else {
                $items[$crud::class] = $crud;
            }
        }

        return $items;
    }
}