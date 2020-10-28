<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Crud;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CrudListener implements EventSubscriberInterface
{

    /**
     * @var Crud
     */
    private $activeCrud;

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController'
        ];
    }

    public function onKernelController(ControllerEvent $event)
    {

        if (!$event->isMasterRequest()) {
            return;
        }

        $controller = $event->getController();

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof Crud) {
            $this->activeCrud = $controller;
            $controller->load();
        }

    }

    public function guessEntity()
    {
        if ($this->activeCrud === null) {
            return null;
        }

        return $this->activeCrud->guessEntity();
    }


}