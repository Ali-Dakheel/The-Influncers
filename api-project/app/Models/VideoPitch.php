<?php

namespace App\Models;

use App\Enums\VideoPitchStatus;
use Database\Factories\VideoPitchFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'influencer_id',
    'brand_id',
    'campaign_id',
    'video_url',
    'message',
    'status',
    'reviewed_at',
    'decision_note',
])]
class VideoPitch extends Model
{
    /** @use HasFactory<VideoPitchFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => VideoPitchStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'influencer_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(User::class, 'brand_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
