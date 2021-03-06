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
     * @var bool
     */
    protected $required = true;

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
    protected $displayedInForm = true;

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

    /**
     * @var string
     */
    protected $placeholder;


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

    public function isDisplayedInForm(): bool
    {
        return $this->displayedInForm;
    }

    public function setDisplayedInForm(bool $displayedInEdition): self
    {
        $this->displayedInForm = $displayedInEdition;

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

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function getPlaceholder(): ?string
    {
        return $this->placeholder;
    }

    public function setPlaceholder(?string $placeholder): self
    {
        $this->placeholder = $placeholder;

        return $this;
    }

}