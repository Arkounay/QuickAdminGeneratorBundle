<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Twig\Runtime;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Menu\MenuInterface;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Action;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

class QagExtensionRuntime implements RuntimeExtensionInterface
{
    private ?array $_cache = null;

    public function __construct(
        private array $config,
        private RouterInterface $router,
        private RequestStack $requestStack,
        private MenuInterface $menu,
        private Environment $twig
    ) {}
    
    private function getMenuItems(): iterable
    {
        return $this->menu->generateMenu();
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

    public function icon(string $name, int $width = 16, int $height = 16, ?string $class = null): string
    {
        return $this->twig->render('@ArkounayQuickAdminGenerator/extensions/_icon_renderer.html.twig', [
            'name' => $name,
            'width' => $width,
            'height' => $height,
            'class' => $class
        ]);
    }

    public function getQag(): array
    {
        if ($this->_cache === null) {
            $this->_cache = ['menu_items' => $this->getMenuItems(), 'config' => $this->config];
        }
        return $this->_cache;
    }

}
