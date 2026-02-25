<?php

declare(strict_types=1);

namespace App\Entities;

class Faq
{
    public function __construct(public string $question, public string $answer) {}

    public function toArray(): array
    {
        return (array) $this;
    }
}
