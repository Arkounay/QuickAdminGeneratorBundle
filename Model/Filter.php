<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\FilterForm;
use function Symfony\Component\String\u;


class Filter implements Listable
{

    public function __construct(protected string $index, protected FilterForm $filterForm, protected ?string $label = null)
    {
        if ($label === null) {
            $this->label = u($index)->title()->toString();
        }
    }

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getFilterForm(): FilterForm
    {
        return $this->filterForm;
    }

    public function setFilterForm(FilterForm $filterForm): self
    {
        $this->filterForm = $filterForm;

        return $this;
    }

}