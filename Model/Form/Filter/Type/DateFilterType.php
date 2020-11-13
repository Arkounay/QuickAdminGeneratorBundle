<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class DateFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('choice', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'attr' => ['class' => 'filter-choice vanilla'],
                'choices' => [
                    'Before' => '<',
                    'After' => '>',
                    'Between' => 'between',
                    'Not in' => 'not in',
                ]
            ])
            ->add('date', $this->dateType(), [
                'required' => false,
                'widget' => 'single_text'
            ])
            ->add('date_start', $this->dateType(), [
                'required' => false,
                'widget' => 'single_text'
            ])
            ->add('date_end', $this->dateType(), [
                'required' => false,
                'widget' => 'single_text'
            ])
        ;
    }

    public function dateType(): string
    {
        return DateType::class;
    }

}
