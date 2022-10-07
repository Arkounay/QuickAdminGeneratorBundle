<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Filter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormInterface;

class EnumFilter extends GenericFilter
{

    public function __construct(protected string $enumType, protected array $options = []) {}

    protected function getOptions(Filter $filter): array
    {
        return array_merge(parent::getOptions($filter), [
            'class' => $this->enumType,
            'placeholder' => 'All',
        ], $this->options);
    }

    protected function getType(): string
    {
        return EnumType::class;
    }

    public function addToQueryBuilder(QueryBuilder $builder, FormInterface $form, Filter $filter): QueryBuilder
    {
        $index = $filter->getIndex();

        return $builder->andWhere("e.$index = :$index")
            ->setParameter($index, $form->getData()[$index]);
    }

}