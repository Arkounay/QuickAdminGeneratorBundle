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
        $entityOptions = [
            'required' => false,
            'class' => $options['class']
        ];

        if ($options['query_builder'] ?? false) {
            $entityOptions['query_builder'] = $options['query_builder'];
        }

        $builder
            ->add('choice', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'choices' => [
                    'Equal' => '=',
                    'Different' => '!=',
                ]
            ])
            ->add('entity', EntityType::class, $entityOptions)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('class');
        $resolver->setDefined('query_builder');
    }

}
