<?php

namespace App\Http\Requests\Campaign;

use App\Enums\CampaignCategory;
use App\Enums\CampaignFormat;
use App\Enums\CampaignObjective;
use App\Enums\Platform;
use App\Models\Campaign;
use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Campaign::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'deliverables' => ['nullable', 'string'],
            'mood_board_title' => ['nullable', 'string', 'max:255'],
            'mood_board_description' => ['nullable', 'string'],
            'category' => ['required', Rule::in(CampaignCategory::values())],
            'country_id' => ['nullable', Rule::exists(Country::class, 'id')],
            'platforms' => ['required', 'array', 'min:1'],
            'platforms.*' => [Rule::in(Platform::values())],
            'format' => ['required', Rule::in(CampaignFormat::values())],
            'objective' => ['required', Rule::in(CampaignObjective::values())],
            'budget_cents' => ['required', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'application_deadline' => ['nullable', 'date'],
        ];
    }
}
