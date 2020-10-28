<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model;

class Field implements Listable
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
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $twig = '@ArkounayQuickAdminGenerator/crud/fields/_default.html.twig';

    /**
     * @var bool
     */
    protected $sortable;

    /**
     * @var string
     */
    protected $sortQuery;

    /**
     * @var string
     */
    protected $associationMapping;

    /**
     * @var bool
     */
    protected $displayedInList = true;

    /**
     * @var bool
     */
    protected $displayedInEdition = true;

    /**
     * @var string - null if not sorted by default
     */
    protected $defaultSortDirection;

    /**
     * @var string
     */
    protected $formType;

    /**
     * @var string
     */
    protected $formClass;


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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTwig(): ?string
    {
        return $this->twig;
    }

    public function setTwig(?string $twig): self
    {
        $this->twig = $twig;

        return $this;
    }

    public function isSortable(): ?bool
    {
        return $this->sortable;
    }

    public function setSortable(?bool $sortable): self
    {
        $this->sortable = $sortable;

        return $this;
    }

    public function getSortQuery(): ?string
    {
        return $this->sortQuery;
    }

    public function setSortQuery(?string $sortQuery): self
    {
        $this->sortQuery = $sortQuery;

        return $this;
    }

    public function getAssociationMapping(): ?string
    {
        return $this->associationMapping;
    }

    public function setAssociationMapping(?string $associationMapping): self
    {
        $this->associationMapping = $associationMapping;

        return $this;
    }

    public function isDisplayedInList(): bool
    {
        return $this->displayedInList;
    }

    public function setDisplayedInList(bool $displayedInList): self
    {
        $this->displayedInList = $displayedInList;

        return $this;
    }

    public function isDisplayedInEdition(): bool
    {
        return $this->displayedInEdition;
    }

    public function setDisplayedInEdition(bool $displayedInEdition): self
    {
        $this->displayedInEdition = $displayedInEdition;

        return $this;
    }

    public function getDefaultSortDirection(): ?string
    {
        return $this->defaultSortDirection;
    }

    public function setDefaultSortDirection(?string $defaultSortDirection): void
    {
        $this->defaultSortDirection = $defaultSortDirection;
    }

    public function getFormType(): ?string
    {
        return $this->formType;
    }

    public function setFormType(?string $formType): self
    {
        $this->formType = $formType;

        return $this;
    }

    public function getFormClass(): ?string
    {
        return $this->formClass;
    }

    public function setFormClass(?string $formClass): self
    {
        $this->formClass = $formClass;

        return $this;
    }

}