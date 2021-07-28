<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Controller;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Entity\Category;

class CategoryDisabledController extends Crud
{

    public function getEntity(): string
    {
        return Category::class;
    }

    public function isEnabled(): bool
    {
        return false;
    }

    public function getRoute(): string
    {
        return 'disabled';
    }

    public function getName(): string
    {
        return 'Category disabled';
    }

}
