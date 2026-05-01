<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use Database\Factories\ApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'campaign_id',
    'influencer_id',
    'status',
    'pitch',
    'proposed_price_cents',
    'currency',
    'applied_at',
    'decided_at',
    'decided_by',
    'decision_note',
])]
class Application extends Model
{
    /** @use HasFactory<ApplicationFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => ApplicationStatus::class,
            'proposed_price_cents' => 'integer',
            'applied_at' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'influencer_id');
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function drafts(): HasMany
    {
        return $this->hasMany(Draft::class);
    }

    public function outcome(): HasOne
    {
        return $this->hasOne(Outcome::class);
    }

    public function latestDraft(): HasOne
    {
        return $this->hasOne(Draft::class)->latestOfMany('revision_number');
    }

    public function isPending(): bool
    {
        return $this->status === ApplicationStatus::Pending;
    }

    public function isAccepted(): bool
    {
        return $this->status === ApplicationStatus::Accepted;
    }
}
