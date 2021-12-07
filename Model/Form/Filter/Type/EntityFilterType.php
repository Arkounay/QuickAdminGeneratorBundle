<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\Type;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityFilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('choice', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'choices' => [
                    'Equal' => '=',
                    'Different' => '!=',
                ]
            ])
            ->add('entity', EntityType::class, [
                'required' => false,
                'class' => $options['class']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('class');
    }

}
