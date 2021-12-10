<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{

    public function __construct(private array $config){}

    public function dashboard(): Response
    {
        if ($redirectRoute = $this->config['dashboard_route_redirection']) {
            return $this->redirectToRoute($redirectRoute);
        }

        return $this->render('@ArkounayQuickAdminGenerator/crud/index.html.twig');
    }

}