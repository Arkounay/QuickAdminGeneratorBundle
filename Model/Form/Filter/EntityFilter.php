<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Filter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\Type\EntityFilterType;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;

class EntityFilter extends GenericFilter
{

    /**
     * @var string
     */
    private $class;

    /**
     * @var array
     */
    protected $options;

    public function __construct(string $class, array $options = [])
    {
        $this->class = $class;
        $this->options = $options;
    }

    protected function getOptions(Filter $filter): array
    {
        return array_merge(parent::getOptions($filter), $this->options, ['class' => $this->class]);
    }

    protected function getType(): string
    {
        return EntityFilterType::class;
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