<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Field
{

    /**
     * @var ?string
     */
    public $label;

    /**
     * @var ?string
     */
    public $twigName;

    /**
     * @var ?bool
     */
    public $sortable = null;

    /**
     * @var ?string
     */
    public $formClass;

    /**
     * @var ?string
     */
    public $formType;

    /**
     * @var ?bool
     */
    public $required = null;

    /**
     * @var ?string
     */
    public $placeholder;

    /**
     * @var ?string
     */
    public $help;

    public function __construct(?string $label = null, ?string $twigName = null, ?bool $sortable = null, ?string $formClass = null, ?string $formType = null, ?bool $required = null, ?string $help = null, ?string $placeholder = null)
    {
        $this->label = $label;
        $this->twigName = $twigName;
        $this->sortable = $sortable;
        $this->formClass = $formClass;
        $this->formType = $formType;
        $this->required = $required;
        $this->help = $help;
        $this->placeholder = $placeholder;
    }

}