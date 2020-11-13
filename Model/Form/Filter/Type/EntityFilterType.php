<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\Type;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityFilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('choice', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'attr' => ['class' => 'vanilla'],
                'choices' => [
                    'Equal' => '=',
                    'Different' => '!=',
                ]
            ])
            ->add('entity', EntityType::class, [
                'required' => false,
                'attr' => ['class' => 'vanilla'],
                'class' => $options['class']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('class');
    }

}
