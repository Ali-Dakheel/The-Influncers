<?php

namespace App\Http\Requests\Reporting;

use App\Enums\CampaignCategory;
use App\Enums\Platform;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AggregateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'brand_id' => ['nullable', 'exists:users,id'],
            'influencer_id' => ['nullable', 'exists:users,id'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'platform' => ['nullable', Rule::in(Platform::values())],
            'category' => ['nullable', Rule::in(CampaignCategory::values())],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ];
    }
}
