<?php

namespace App\Http\Requests\Campaign;

use App\Enums\CampaignCategory;
use App\Enums\CampaignFormat;
use App\Enums\CampaignObjective;
use App\Enums\Platform;
use App\Models\Country;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('campaign')) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'deliverables' => ['nullable', 'string'],
            'mood_board_title' => ['nullable', 'string', 'max:255'],
            'mood_board_description' => ['nullable', 'string'],
            'category' => ['sometimes', 'required', Rule::in(CampaignCategory::values())],
            'country_id' => ['nullable', Rule::exists(Country::class, 'id')],
            'platforms' => ['sometimes', 'required', 'array', 'min:1'],
            'platforms.*' => [Rule::in(Platform::values())],
            'format' => ['sometimes', 'required', Rule::in(CampaignFormat::values())],
            'objective' => ['sometimes', 'required', Rule::in(CampaignObjective::values())],
            'budget_cents' => ['sometimes', 'required', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'application_deadline' => ['nullable', 'date'],
        ];
    }
}
