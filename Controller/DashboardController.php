<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController implements AdminInterface
{

    public function dashboard(): Response
    {
        return $this->render('@ArkounayQuickAdminGenerator/crud/index.html.twig');
    }

}