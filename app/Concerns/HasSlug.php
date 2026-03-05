<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Support\Str;

trait HasSlug
{
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model): void {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        static::updating(function ($model): void {
            if ($model->isDirty('name')) {
                $model->slug = Str::slug($model->name);
            }
        });
    }
}
