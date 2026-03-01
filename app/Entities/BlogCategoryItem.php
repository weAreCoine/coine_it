<?php

declare(strict_types=1);

namespace App\Entities;

use App\Models\Category;
use Illuminate\Contracts\Support\Arrayable;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class BlogCategoryItem implements Arrayable
{
    public string $name;

    public string $slug;

    public static function fromCategory(Category $category): self
    {
        $instance = new self;
        $instance->name = $category->name;
        $instance->slug = $category->slug;

        return $instance;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return (array) $this;
    }
}
