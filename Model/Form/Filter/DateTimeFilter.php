<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\Type\DateTimeFilterType;

class DateTimeFilter extends DateFilter
{
    protected function getType(): string
    {
        return DateTimeFilterType::class;
    }

}
