<?php

declare(strict_types=1);

namespace App\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class NavigationItem implements Arrayable
{
    public string $href;

    public bool $isCurrent = false;

    public function __construct(
        public string $title,
        public ?string $routeName,
        public bool $isExternal = false,
        public bool $targetBlank = false,
        public bool $isPlaceholder = false,
        /** @var \App\Entities\NavigationItem[] */
        public array $subItems = [],
        public bool $isCallToAction = false,
    ) {

        $this->isPlaceholder = $this->isPlaceholder || $this->routeName === null;

        if ($this->routeName === null) {
            $this->routeName = '#';
            $this->isExternal = false;
        }

        if (!$this->isPlaceholder) {
            if (!$this->isExternal) {
                $this->href = route($this->routeName);
                $this->isCurrent = request()->routeIs($this->routeName);
            } else {
                $this->href = $routeName;
            }
        } else {
            $this->href = '#';
            $this->routeName = null;
            $this->isExternal = false;
        }
    }

    public function toArray(): array
    {
        return (array) $this;
    }
}
