<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Filter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\Type\DateFilterType;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\Type\DateTimeFilterType;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\Type\IntegerFilterType;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class IntegerFilter extends GenericFilter
{
    protected function getType(): string
    {
        return IntegerFilterType::class;
    }

    public function addToQueryBuilder(QueryBuilder $builder, FormInterface $form, Filter $filter): QueryBuilder
    {
        $index = $filter->getIndex();

        $formData = $form->get($index)->getData();

        $choice = $formData['choice'];

        $start = $formData['number_start'];
        $end = $formData['number_end'];

        if ($start > $end) {
            $tmp = $start;
            $start = $end;
            $end = $tmp;
        }

        switch ($choice) {
            case 'between':
                return $builder->andWhere("e.$index > :{$index}_start")
                    ->andWhere("e.$index < :{$index}_end")
                    ->setParameter("{$index}_start", $start)
                    ->setParameter("{$index}_end", $end);
            case 'not in':
                return $builder->andWhere("e.$index < :{$index}_start or e.$index > :{$index}_end")
                    ->setParameter("{$index}_start", $start)
                    ->setParameter("{$index}_end", $end);
            default:
                return $builder->andWhere("e.$index $choice :$index")
                    ->setParameter($index, $formData['number']);
        }

    }

    public function isEmpty($data): bool
    {
        if (!isset($data['choice'])) {
            return false;
        }

        switch ($data['choice']) {
            case 'between':
            case 'not in':
                return empty($data['number_start']) && empty($data['number_end']);
            default:
                return empty($data['number']);
        }
    }

}
