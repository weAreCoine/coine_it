<?php

namespace App\Models;

use App\Events\LeadCreated;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    public $timestamps = true;

    protected $guarded = [];

    protected $dispatchesEvents = [
        'created' => LeadCreated::class,
    ];

    protected function casts(): array
    {
        return [
            'terms' => 'boolean',
            'updated_at' => 'datetime',
            'created_at' => 'datetime',
            'services' => 'json',
        ];
    }
}
