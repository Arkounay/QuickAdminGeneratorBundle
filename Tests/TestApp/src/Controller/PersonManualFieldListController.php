<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Controller;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Fields;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Entity\Person;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test fetchmode manual
 */
class PersonManualFieldListController extends Crud
{

    public function getEntity(): string
    {
        return Person::class;
    }

    public function getRoute(): string
    {
        return 'person-field';
    }

    protected function getFormFields(): Fields
    {
        return parent::getListingFields()
            ->add('lastname')
            ->add('id')
            ->moveToFirstPosition('lastname')
            ->remove('id');
    }

    protected function getExportFields(): Fields
    {
        return parent::getExportFields()
            ->remove('firstname');
    }

    public function isExportable(): bool
    {
        return true;
    }


}
