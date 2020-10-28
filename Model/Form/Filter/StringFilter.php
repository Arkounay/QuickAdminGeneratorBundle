<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Filter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class StringFilter extends GenericFilter
{

    public function addToQueryBuilder(QueryBuilder $builder, FormInterface $form, Filter $filter): QueryBuilder
    {
        $index = $filter->getIndex();

        return $builder->andWhere("e.$index LIKE :$index")
            ->setParameter($index, "%{$form->get($index)->getData()}%");
    }

}