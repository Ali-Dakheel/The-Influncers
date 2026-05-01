<?php

namespace App\Models;

use App\Enums\CampaignCategory;
use App\Enums\CampaignFormat;
use App\Enums\CampaignObjective;
use App\Enums\Platform;
use Database\Factories\OutcomeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'campaign_id',
    'application_id',
    'influencer_id',
    'platform',
    'category',
    'country_id',
    'format',
    'objective',
    'final_post_url',
    'reach',
    'engagement',
    'conversions',
    'cost_per_result_cents',
    'paid_price_cents',
    'recorded_at',
])]
class Outcome extends Model
{
    /** @use HasFactory<OutcomeFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'platform' => Platform::class,
            'category' => CampaignCategory::class,
            'format' => CampaignFormat::class,
            'objective' => CampaignObjective::class,
            'reach' => 'integer',
            'engagement' => 'integer',
            'conversions' => 'integer',
            'cost_per_result_cents' => 'integer',
            'paid_price_cents' => 'integer',
            'recorded_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'influencer_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
