<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\Type;


use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class DateTimeFilterType extends DateFilterType
{

    public function dateType(): string
    {
        return DateTimeType::class;
    }

}
