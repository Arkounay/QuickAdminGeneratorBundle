<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Controller;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Tests\TestApp\src\Entity\Person;

/**
 * Test fetchmode manual
 */
class PersonController extends Crud
{

    public function getEntity(): string
    {
        return Person::class;
    }

}
