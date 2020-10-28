<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model;

use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Form\Filter\FilterForm;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Model\Listable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class Filter implements Listable
{

    /**
     * @var string
     */
    protected $index;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var FilterForm
     */
    protected $filterForm;

    public function __construct(string $index)
    {
        $this->index = $index;
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

    public function getFilterForm(): ?FilterForm
    {
        return $this->filterForm;
    }

    public function setFilterForm(FilterForm $filterForm): self
    {
        $this->filterForm = $filterForm;

        return $this;
    }

}