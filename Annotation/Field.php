<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Annotation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @Annotation
 * @NamedArgumentConstructor
 */
#[\Attribute(\Attribute::TARGET_PROPERTY|\Attribute::TARGET_METHOD)]
class Field
{

    public ?string $label;
    public ?string $twigName;
    public ?bool $sortable = null;
    public ?string $formClass;
    public ?string $formType;
    public array $options;
    public ?bool $required = null;
    public ?string $placeholder;
    public ?string $help;
    public ?int $position;
    public mixed $payload;

    public function __construct(?string $label = null, ?string $twigName = null, ?bool $sortable = null, ?string $formClass = null, ?string $formType = null, array $options = [], ?bool $required = null, ?string $help = null, ?string $placeholder = null, ?int $position = null, $payload = null)
    {
        $this->label = $label;
        $this->twigName = $twigName;
        $this->sortable = $sortable;
        $this->formClass = $formClass;
        $this->formType = $formType;
        $this->options = $options;
        $this->required = $required;
        $this->help = $help;
        $this->placeholder = $placeholder;
        $this->position = $position;
        $this->payload = $payload;
    }

}