<?php

namespace App\Models;

use App\Enums\LeadStage;
use App\Events\LeadCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Lead extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(fn (Lead $lead) => $lead->uuid ??= (string) Str::uuid());
    }

    protected $dispatchesEvents = [
        'created' => LeadCreated::class,
    ];

    protected function casts(): array
    {
        return [
            'terms' => 'boolean',
            'quiz_answers' => 'array',
            'services' => 'array',
            'updated_at' => 'datetime',
            'created_at' => 'datetime',
            'stage' => LeadStage::class,
        ];
    }
}
