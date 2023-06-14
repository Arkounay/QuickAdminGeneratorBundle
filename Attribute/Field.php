<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY|\Attribute::TARGET_METHOD)]
class Field
{

    public function __construct(
        public ?string $label = null,
        public ?string $twigName = null,
        public ?bool $sortable = null,
        public ?string $formClass = null,
        public ?string $formType = null,
        public array $options = [],
        public ?bool $required = null,
        public ?string $help = null,
        public ?string $placeholder = null,
        public ?int $position = null,
        public $payload = null
    ) {}

}