<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Sort
{

    public $direction;

    public function __construct(?string $direction = 'desc')
    {
        $this->direction = $direction;
    }

}