<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
readonly class Crud
{

    final public const FETCH_AUTO = 'auto';
    final public const FETCH_MANUAL = 'manual';

    /**
     * @var string auto|manual
     * Check if fields should be fetched automatically or manually
     */
    public ?string $fetchMode;

    public function __construct(?string $fetchMode = self::FETCH_AUTO)
    {
        $this->fetchMode = $fetchMode;
    }

}