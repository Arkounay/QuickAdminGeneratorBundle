<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Filter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;

class BooleanFilter extends GenericFilter
{
    /**
     * @return array<string, mixed>
     */
    protected function getOptions(Filter $filter): array
    {
        return array_merge(parent::getOptions($filter), [
            'placeholder' => 'All',
            'choices' => [
                'Yes' => true,
                'No' => false
            ]
        ]);
    }

    protected function getType(): string
    {
        return ChoiceType::class;
    }

    public function addToQueryBuilder(QueryBuilder $builder, FormInterface $form, Filter $filter): QueryBuilder
    {
        $index = $filter->getIndex();

        return $builder->andWhere("e.$index = :$index")
            ->setParameter($index, $form->getData()[$index]);
    }

    /**
     * @param mixed $data
     */
    public function isEmpty(mixed $data): bool
    {
        return $data === '';
    }

}
