<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Menu;


class MenuItem
{

    /**
     * @var string
     */
    protected $label;

    /**
     * @var ?string
     */
    protected $url;

    /**
     * @var ?string
     */
    protected $icon;

    /**
     * @var ?int
     */
    protected $badgeNumber;

    /**
     * @var ?string
     */
    protected $badgeClass;

    /**
     * @var bool
     */
    protected $active = false;

    /**
     * @var ?MenuItem[]
     */
    protected $children;

    /**
     * @var string[]
     */
    protected $attributes = [];

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): void
    {
        $this->icon = $icon;
    }

    public function getBadgeNumber(): ?int
    {
        return $this->badgeNumber;
    }

    public function setBadgeNumber(?int $badgeNumber): void
    {
        $this->badgeNumber = $badgeNumber;
    }

    public function getBadgeClass(): ?string
    {
        return $this->badgeClass;
    }

    public function setBadgeClass(?string $badgeClass): void
    {
        $this->badgeClass = $badgeClass;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return MenuItem[]|null
     */
    public function getChildren(): ?array
    {
        return $this->children;
    }

    public function setChildren(?array $children): void
    {
        $this->children = $children;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function addAttribute(string $key, string $value): void
    {
        if (!isset($this->attributes[$key])) {
            $this->attributes[$key] = $value;
        } else {
            $this->attributes[$key] .= ' ' . $value;
        }
    }

}