<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
readonly class Sort
{

    public function __construct(public ?string $direction = 'desc'){}

}