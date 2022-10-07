<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $formClass;

    /**
     * @var string
     */
    protected $placeholder;

    /**
     * @var string
     */
    protected $help;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var mixed for custom events
     */
    protected $payload;

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

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;

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

    public function getHelp(): ?string
    {
        return $this->help;
    }

    public function setHelp(?string $help): void
    {
        $this->help = $help;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function setPayload($payload): void
    {
        $this->payload = $payload;
    }

    public function guessFormOptions(): array
    {
        $options = ['label' => $this->getLabel(), 'required' => $this->isRequired()];
        if ($this->getFormClass() !== null) {
            $options['attr'] = ['class' => $this->getFormClass()];
        }
        if ($this->getPlaceholder() !== null) {
            $options['placeholder'] = $this->getPlaceholder();
        }
        if ($this->getHelp() !== null) {
            $options['help'] = $this->getHelp();
        }

        switch ($this->getType()) {
            case 'enum':
                $options['expanded'] = true;
                $options['class'] = $this->getAssociationMapping();
                break;
            case 'datetime_immutable':
                $options['input'] = 'datetime_immutable';
            case 'datetime':
            case 'date':
                $options['widget'] = 'single_text';
                break;
            case 'relation':
                $options['attr']['data-controller'] = 'select2';
                $options['class'] =  $this->getAssociationMapping();
                $options['multiple'] =  false;
                break;
            case 'relation_to_many':
                $options['attr']['data-controller'] = 'select2';
                $options['class'] = $this->getAssociationMapping();
                $options['multiple'] = true;
                $options['by_reference'] = false;
                break;
        }

        $options = array_merge($options, $this->getOptions());

        return $options;
    }

    public function guessFormType(): ?string
    {
        if ($this->getFormType() !== null) {
            return $this->getFormType();
        }

        return match ($this->getType()) {
            'decimal' => TextType::class,
            'enum' => EnumType::class,
            'date' => $this->getFormType() ?? DateType::class,
            'datetime_immutable', 'datetime' => $this->getFormType() ?? DateTimeType::class,
            'relation_to_many', 'relation' => $this->getFormType() ?? EntityType::class,
            default => null,
        };
    }

}