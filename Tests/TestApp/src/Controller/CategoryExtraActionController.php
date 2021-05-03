<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Controller;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Action;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Actions;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Most basic Crud
 */
class CategoryExtraActionController extends Crud
{

    public function getEntity(): string
    {
        return Category::class;
    }

    public function getRoute(): string
    {
        return 'category-extra-actions';
    }

    public function getActions($entity): ?Actions
    {
        return parent::getActions($entity)->add('custom');
    }

    public function getGlobalActions(): ?Actions
    {
        return parent::getGlobalActions()->add((new Action('export'))->addClass('custom-global-action'));
    }

    public function exportAction(): Response
    {
        return new Response(1);
    }

    public function isCreatable(): bool
    {
        return false;
    }

}
