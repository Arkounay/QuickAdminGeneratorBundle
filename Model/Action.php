<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model;


use function Symfony\Component\String\u;

class Action implements Listable
{

    protected ?string $label = null;

    protected ?string $icon = null;

    /** @var string[] */
    protected array $classes = [];

    /**
     * @var string[]
     */
    protected array $dropdownClasses = [];

    /**
     * @var string[]
     */
    protected array $attributes = [];

    protected ?string $customHref = null;

    public function __construct(protected readonly string $index) {}

    public function getIndex(): string
    {
        return $this->index;
    }

    public function getLabel(): ?string
    {
        return $this->label ?? u($this->index)->title()->toString();
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * @param string[] $classes
     */
    public function addSharedClasses(string ...$classes): self
    {
        foreach ($classes as $class) {
            $this->addClass($class);
            $this->addDropDownClass($class);
        }

        return $this;
    }

    /**
     * @param string[] $classes
     */
    public function addClasses(string ...$classes): self
    {
        $this->classes = array_merge($this->classes, $classes);

        return $this;
    }

    public function addClass(string $class): self
    {
        if (!in_array($class, $this->classes)) {
            $this->classes[] = $class;
        }

        return $this;
    }

    public function removeClass(string $class): self
    {
        if (($key = array_search($class, $this->classes)) !== false) {
            unset($this->classes[$key]);
        }

        return $this;
    }


    public function getDropdownClasses(): array
    {
        return $this->dropdownClasses;
    }

    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    public function setAttributes(array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function addAttribute(string $key, string $content): self
    {
        if (isset($this->attributes[$key])) {
            $this->attributes[$key] .= " $content";
        } else {
            $this->attributes[$key] = $content;
        }
        return $this;
    }

    public function addAttributes(array $attributes): self
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }

    public function setModal(Modal $modal): void
    {
        $this->attributes = array_merge($this->attributes, $modal->toAttributes());
    }

    /**
     * @param string[] $classes
     */
    public function addDropDownClasses(string ...$classes): self
    {
        $this->dropdownClasses = array_merge($this->dropdownClasses, $classes);

        return $this;
    }

    public function addDropDownClass(string $class): self
    {
        if (!in_array($class, $this->dropdownClasses)) {
            $this->dropdownClasses[] = $class;
        }

        return $this;
    }

    public function removeDropdownClass(string $class): self
    {
        if (($key = array_search($class, $this->dropdownClasses)) !== false) {
            unset($this->dropdownClasses[$key]);
        }

        return $this;
    }

    public function getCustomHref(): ?string
    {
        return $this->customHref;
    }

    public function setCustomHref(?string $customHref): self
    {
        $this->customHref = $customHref;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

}