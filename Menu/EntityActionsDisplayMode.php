<?php

namespace Arkounay\Bundle\QuickAdminGeneratorBundle\Menu;

enum EntityActionsDisplayMode
{
    case Dropdown;
    case ExpandedGroup;
    case Expanded;

    public function isExpanded(): bool
    {
        return $this === self::Expanded || $this === self::ExpandedGroup;
    }

    public function getWrapperClasses(): string
    {
        return match ($this) {
            self::Dropdown, self::ExpandedGroup => 'btn-group btn-group-table',
            default => 'btn-group-table'
        };
    }
}