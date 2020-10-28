<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Filter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

abstract class FilterForm
{

    abstract public function addToFormBuilder(FormBuilderInterface $builder, Filter $filter): void;

    abstract public function addToQueryBuilder(QueryBuilder $builder, FormInterface $form, Filter $filter): QueryBuilder;

    abstract public function isEmpty($data): bool;

}
