<?php

namespace App\Models;

use App\Enums\CampaignCategory;
use App\Enums\CampaignFormat;
use App\Enums\CampaignObjective;
use App\Enums\CampaignState;
use Database\Factories\CampaignFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'brand_id',
    'title',
    'description',
    'deliverables',
    'mood_board_title',
    'mood_board_description',
    'category',
    'country_id',
    'platforms',
    'format',
    'objective',
    'budget_cents',
    'currency',
    'state',
    'starts_on',
    'ends_on',
    'application_deadline',
    'published_at',
    'paused_at',
    'closed_at',
    'completed_at',
])]
class Campaign extends Model
{
    /** @use HasFactory<CampaignFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'category' => CampaignCategory::class,
            'format' => CampaignFormat::class,
            'objective' => CampaignObjective::class,
            'state' => CampaignState::class,
            'platforms' => 'array',
            'budget_cents' => 'integer',
            'starts_on' => 'date',
            'ends_on' => 'date',
            'application_deadline' => 'datetime',
            'published_at' => 'datetime',
            'paused_at' => 'datetime',
            'closed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(User::class, 'brand_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function outcomes(): HasMany
    {
        return $this->hasMany(Outcome::class);
    }

    public function isDraft(): bool
    {
        return $this->state === CampaignState::Draft;
    }

    public function isPublished(): bool
    {
        return $this->state === CampaignState::Published;
    }

    public function isPaused(): bool
    {
        return $this->state === CampaignState::Paused;
    }

    public function isClosed(): bool
    {
        return $this->state === CampaignState::Closed;
    }
}
