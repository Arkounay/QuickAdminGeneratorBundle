<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Model;


class Action implements Listable
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
    protected $icon;

    /**
     * @var string[]
     */
    protected $classes = [];

    /**
     * @var string[]
     */
    protected $dropdownClasses = [];

    /**
     * @var string
     */
    protected $customHref;

    public function __construct(string $index)
    {
        $this->index = $index;
        $this->label = $index;
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