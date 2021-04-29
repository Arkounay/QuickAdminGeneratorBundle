<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation;

/**
 * @Annotation
 */
class Crud
{

    public const FETCH_AUTO = 'auto';
    public const FETCH_MANUAL = 'manual';

  // In v2.0, will have class-wide attributes such as "name"

    /**
     * @var string auto|manual
     * Check if fields should be fetch automatically or manually
     */
    public $fetchMode = self::FETCH_AUTO;



}