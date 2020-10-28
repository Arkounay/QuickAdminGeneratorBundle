<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Controller;


use Symfony\Component\HttpFoundation\Response;

interface AdminInterface
{

    public function dashboard(): Response;

}