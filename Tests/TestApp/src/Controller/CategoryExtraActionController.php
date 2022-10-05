<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Controller;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Action;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Actions;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Modal;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Entity\Category;
use Symfony\Component\HttpFoundation\Response;

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

    public function getActions($entity): Actions
    {
        $actions = parent::getActions($entity)->add('custom');
        if ($actions->contains('custom')) {
            $actions['custom']->setLabel('My custom action');
        }

        $modalAction = new Action('modal');
        $modal = new Modal($this->translator, $entity->getName());
        $modal->setHtml("This is the category <strong>{$entity->getName()}</strong>");
        $modal->setHasUpperRightCloseButton(false);
        $modal->setModalClasses('modal-dialog-centered');
        $modalAction->setModal($modal);
        $actions->add($modalAction);

        return $actions;
    }

    public function getGlobalActions(): Actions
    {
        return parent::getGlobalActions()
            ->add((new Action('stats'))->addClass('custom-global-action'))
            ->add((new Action('stats 2'))->setLabel('My custom action label'))
            ->moveToFirstPosition('stats 2')
        ;
    }

    public function statsAction(): Response
    {
        return new Response(1);
    }

    public function isCreatable(): bool
    {
        return false;
    }

}
