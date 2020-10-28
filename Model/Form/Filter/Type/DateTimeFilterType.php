<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeFilterType extends DateFilterType
{

    public function dateType(): string
    {
        return DateTimeType::class;
    }

}
