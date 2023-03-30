<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\EventSubscriber;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use function Symfony\Component\String\u;

class ControllerSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $controller = $event->getController();

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (!is_array($controller)) {
           return;
        }

        [$controller, $method] = $controller;

        if ($controller instanceof Crud) {
            $method = (u($method)->beforeLast('Action'));
            $controller->checkSecurity($method, $controller->guessEntity());
        }
    }


}