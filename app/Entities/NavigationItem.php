<?php

declare(strict_types=1);

namespace App\Entities;

class NavigationItem
{
    public string $href;

    public bool $isCurrent = false;

    public function __construct(
        public string $title,
        public string $routeName,
        public bool $isExternal = false,
        public bool $targetBlank = false
    ) {
        if (!$isExternal) {
            $this->href = route($this->routeName);
            $this->isCurrent = request()->routeIs($this->routeName);
        } else {
            $this->href = $routeName;
        }
    }

    public function toArray(): array
    {
        return (array) $this;
    }
}
