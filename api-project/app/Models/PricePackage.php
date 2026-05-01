<?php

namespace App\Models;

use Database\Factories\PricePackageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'name',
    'description',
    'items',
    'discount_pct',
    'total_cents',
    'currency',
])]
class PricePackage extends Model
{
    /** @use HasFactory<PricePackageFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'discount_pct' => 'integer',
            'total_cents' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
