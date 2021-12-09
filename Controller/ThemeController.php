<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ThemeController extends AbstractController
{

    public function switchTheme(Request $request, SessionInterface $session): Response
    {
        $theme = $request->getContent();
        $session->set('theme', $theme);
        return new Response();
    }

}