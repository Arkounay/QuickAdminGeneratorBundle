<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation;

/**
 * @Annotation
 */
class Field
{

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $twigName;

    /**
     * @var bool
     */
    public $sortable = null;

    /**
     * @var string
     */
    public $formClass;

    /**
     * @var string
     */
    public $formType;

    /**
     * @var bool
     */
    public $required = null;

    /**
     * @var string
     */
    public $placeholder;

}