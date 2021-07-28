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
            // Generate menu from all the cruds if nothing is specifying in yaml, alphabetically
            foreach ($this->cruds as $crud) {
                /** @var Crud $crud */
                if ($crud->isEnabled()) {
                    $cruds[$this->translator->trans($crud->getPluralName())] = $crud;
                }
            }
            ksort($cruds);
            $items = $cruds;
        } elseif (is_iterable($this->config['menu']['items'])) {
            // Create menu from yaml
            $items = $this->getCrudAsKArrayWithClassKeys($this->cruds);
            foreach ($this->config['menu']['items'] as $item) {
                $menuItem = $this->createMenuItem($items, $item, $request);
                if ($menuItem !== null) {
                    $menuItems[] = $menuItem;
                }
            }
            return $menuItems;
        }

        foreach ($cruds as $crud) {
            $menuItem = $this->createMenuItem($items, $crud, $request);
            if ($menuItem !== null) {
                $menuItems[] = $menuItem;
            }
        }

        return $menuItems;
    }

    protected function createDashboardMenuItem(Request $request): ?MenuItem
    {
        $dashboard = new MenuItem('Dashboard');
        $dashboard->setUrl($this->router->generate('qag.dashboard'));
        $dashboard->setActive($request->attributes->get('_route') === 'qag.dashboard');
        return $dashboard;
    }

    /**
     * @param array $cruds available cruds controller
     * @param $node string|array the yaml item. Can be a crud class or an array
     * @param Request $request
     * @return MenuItem|null null if nothing to add for this specific node
     */
    protected function createMenuItem(array $cruds, $node, Request $request): ?MenuItem
    {
        if (is_string($node)) {
            if (!isset($cruds[$node])) {
                throw new InvalidConfigurationException("Class '$node' not found in Crud list");
            }
            $crud = $cruds[$node];
            if (!$crud->isEnabled()) {
                return null;
            }
            $route = $this->router->generate('qag.' . $crud->getRoute());
            $menuItem = new MenuItem($crud->getPluralName());
            $menuItem->setUrl($route);
            if ($request->attributes->get('_route') !== 'qag.dashboard' && strpos($route, $request->getPathInfo()) !== false) {
                $menuItem->setActive($route);
            }
        } else {
            if (!isset($node['label'])) {
                throw new InvalidConfigurationException('Menu item needs to be a Crud class or have a label key');
            }
            $menuItem = new MenuItem($node['label']);
            if (isset($node['url'])) {
                $menuItem->setUrl($node['url']);
            }
            if (isset($node['children'])) {
                $children = [];
                foreach ($node['children'] as $childNode) {
                    $child = $this->createMenuItem($cruds, $childNode, $request);
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
                    $items[get_class($child)] = $child;
                }
            } else {
                $items[get_class($crud)] = $crud;
            }
        }

        return $items;
    }
}