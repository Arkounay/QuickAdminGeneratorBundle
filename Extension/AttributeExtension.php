<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Extension;

readonly class AttributeExtension
{

    /**
     * @template T
     * @param class-string<T> $class
     * @return T
     */
    public static function getAttribute(\ReflectionProperty|\ReflectionMethod $reflection, string $class): mixed
    {
        $res = null;
        $attributes = $reflection->getAttributes($class);
        if (!empty($attributes)) {
            $res = $attributes[0]->newInstance();
        }

        return $res;
    }

}