<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Menu;


interface MenuInterface
{

    /**
     * @return \Generator|MenuItem[]
     */
    public function generateMenu(): iterable;

}