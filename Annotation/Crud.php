<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Crud
{

    public const FETCH_AUTO = 'auto';
    public const FETCH_MANUAL = 'manual';

    /**
     * @var string auto|manual
     * Check if fields should be fetched automatically or manually
     */
    public $fetchMode;

    public function __construct(?string $fetchMode = self::FETCH_AUTO)
    {
        $this->fetchMode = $fetchMode;
    }

}