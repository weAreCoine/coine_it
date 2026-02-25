<?php

declare(strict_types=1);

namespace App\Entities;

class ProjectStep
{
    public function __construct(
        public string $title,
        public string $description,
        public ?string $assetUrl = null,
        public ?int $number = 1,
        public string $color = '',
    ) {}
}
