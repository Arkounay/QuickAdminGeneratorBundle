<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Controller;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Most basic Crud
 */
class CategoryController extends Crud
{

    public function getEntity(): string
    {
        return Category::class;
    }

    public function getBadgeNumber(): ?int
    {
        return 25;
    }

}
