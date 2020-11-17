<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Filter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

abstract class GenericFilter extends FilterForm
{

    public function addToFormBuilder(FormBuilderInterface $builder, Filter $filter): void
    {
        $builder->add($filter->getIndex(), $this->getType(), $this->getOptions($filter));
    }

    abstract public function addToQueryBuilder(QueryBuilder $builder, FormInterface $form, Filter $filter): QueryBuilder;

    protected function getType(): string
    {
        return TextType::class;
    }

    protected function getOptions(Filter $filter): array
    {
        return ['required' => false, 'label' => $filter->getLabel()];
    }

    public function isEmpty($data): bool
    {
        return empty($data);
    }

}