<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class IntegerFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('choice', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'attr' => ['class' => 'filter-choice'],
                'choices' => [
                    'Equal' => '=',
                    'Different' => '!=',
                    'Under' => '<',
                    'Above' => '>',
                    'Between' => 'between',
                    'Not in' => 'not in',
                ]
            ])
            ->add('number', IntegerType::class, [
                'required' => false,
            ])
            ->add('number_start', IntegerType::class, [
                'required' => false,
            ])
            ->add('number_end', IntegerType::class, [
                'required' => false,
            ])
        ;
    }

}
