<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Filter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\Type\EntityFilterType;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class EntityFilter extends GenericFilter
{

    /**
     * @var string
     */
    private $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    public function addToFormBuilder(FormBuilderInterface $builder, Filter $filter): void
    {
        $builder->add($filter->getIndex(), EntityFilterType::class, [
            'required' => false,
            'class' => $this->class
        ]);
    }

    public function addToQueryBuilder(QueryBuilder $builder, FormInterface $form, Filter $filter): QueryBuilder
    {
        $index = $filter->getIndex();

        $sign = $form->get($index)->getData()['choice'];
        return $builder->andWhere("e.$index $sign :$index")
            ->setParameter($index, $form->get($index)->getData()['entity']);
    }

    public function isEmpty($data): bool
    {
        return empty($data['entity']);
    }

}