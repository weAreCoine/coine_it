<?php

declare(strict_types=1);

namespace App\Entities;

use Illuminate\Contracts\Support\Arrayable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class NavigationItem implements Arrayable
{
    public bool $isCurrent = false;

    public function __construct(
        public string $title,
        public string $href = '#',
        public bool $isExternal = false,
        public bool $targetBlank = false,
        public bool $isPlaceholder = false,
        /** @var \App\Entities\NavigationItem[] */
        public array $subItems = [],
        public bool $isCallToAction = false,
    ) {
        $this->isPlaceholder = $this->isPlaceholder || $this->href === '#';

        if (! $this->isPlaceholder && ! $this->isExternal) {
            $this->isCurrent = request()->url() === $this->href;
        }
    }

    public function toArray(): array
    {
        return (array) $this;
    }
}
