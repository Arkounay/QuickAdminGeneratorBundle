<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation;

/**
 * @Annotation
 */
class Crud
{

    public const FETCH_AUTO = 'add_auto';
    public const FETCH_MANUAL = 'add_manually';

  // In v2.0, will have class-wide attributes such as "name"

    /**
     * @var string add_auto|add_manually
     * Check if fields should be fetch automatically or manually
     */
    public $fetchMode = self::FETCH_AUTO;



}