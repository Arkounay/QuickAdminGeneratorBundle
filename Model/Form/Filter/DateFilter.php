<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Filter;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\Type\DateFilterType;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;

class DateFilter extends GenericFilter
{
    protected function getType(): string
    {
        return DateFilterType::class;
    }

    public function addToQueryBuilder(QueryBuilder $builder, FormInterface $form, Filter $filter): QueryBuilder
    {
        $index = $filter->getIndex();

        $formData = $form->get($index)->getData();

        $choice = $formData['choice'];

        $start = $formData['date_start'];
        $end = $formData['date_end'];

        if ($start > $end) {
            $tmp = $start;
            $start = $end;
            $end = $tmp;
        }

        return match ($choice) {
            'between' => $builder->andWhere("e.$index > :{$index}_start")
                ->andWhere("e.$index < :{$index}_end")
                ->setParameter("{$index}_start", $start)
                ->setParameter("{$index}_end", $end),
            'not in' => $builder->andWhere("e.$index < :{$index}_start or e.$index > :{$index}_end")
                ->setParameter("{$index}_start", $start)
                ->setParameter("{$index}_end", $end),
            default => $builder->andWhere("e.$index $choice :$index")
                ->setParameter($index, $formData['date']),
        };
    }

    /**
     * @param mixed $data
     */
    public function isEmpty(mixed $data): bool
    {
        if (!isset($data['choice'])) {
            return false;
        }

        return match ($data['choice']) {
            'between', 'not in' => empty($data['date_start']) && empty($data['date_end']),
            default => empty($data['date']),
        };
    }

}
