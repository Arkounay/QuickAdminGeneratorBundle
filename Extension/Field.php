<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Extension;

readonly class Field
{

    public static function twigPath(string $name): string
    {
        return "@ArkounayQuickAdminGenerator/crud/fields/_$name.html.twig";
    }

}