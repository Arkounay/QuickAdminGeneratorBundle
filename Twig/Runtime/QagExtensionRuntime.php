<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Twig\Runtime;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Menu\MenuInterface;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Action;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

class QagExtensionRuntime implements RuntimeExtensionInterface
{
    private ?array $_cache = null;

    public function __construct(
        private readonly array $config,
        private readonly RouterInterface $router,
        private readonly RequestStack $requestStack,
        private readonly MenuInterface $menu,
        private readonly Environment $twig,
        private readonly EventDispatcherInterface $eventDispatcher
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
        } catch (\Exception) {}

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

    public function entityToString($entity): string
    {
        if (is_string($entity)) {
            // used for global search
            return $entity;
        }
        $event = new GenericEvent($entity, ['entity' => $entity]);
        $this->eventDispatcher->dispatch($event, 'qag.events.entity_to_string');
        if ($event->hasArgument('response')) {
            return $event->getArgument('response');
        }
        if (!method_exists($entity, '__toString')) {
            throw new \RuntimeException('Entity ' . $entity::class . ' does not implement __toString. Please override __toString or subscribe to qag.events.entity_to_string');
        }
        return (string)$entity;
    }

    public function getQag(): array
    {
        if ($this->_cache === null) {
            $this->_cache = ['menu_items' => $this->getMenuItems(), 'config' => $this->config];
        }
        return $this->_cache;
    }

}
