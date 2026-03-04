<?php

declare(strict_types=1);

namespace App\Entities;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class Faq
{
    public function __construct(public string $question, public string $answer, public bool $opened = false)
    {
    }

    public function toArray(): array
    {
        return (array) $this;
    }
}
