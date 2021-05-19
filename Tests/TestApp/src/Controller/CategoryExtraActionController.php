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
        $res = parent::getActions($entity)->add('custom');
        if ($res->contains('custom')) {
            $res['custom']->setLabel('My custom action');
        }
        return $res;
    }

    public function getGlobalActions(): ?Actions
    {
        return parent::getGlobalActions()
            ->add((new Action('export'))->addClass('custom-global-action'))
            ->add((new Action('export 2'))->setLabel('My custom action label'))
            ->moveToFirstPosition('export 2')
        ;
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
