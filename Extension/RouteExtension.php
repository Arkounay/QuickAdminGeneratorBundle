<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Extension;

class RouteExtension
{

    public static function humanizeClassName(string $class): string
    {
        $shortName = (new \ReflectionClass($class))->getShortName();
        return self::humanize($shortName);
    }

    public static function humanize(string $text): string
    {
        return ucfirst(strtolower(trim(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $text))));
    }

}