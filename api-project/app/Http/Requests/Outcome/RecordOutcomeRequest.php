<?php

namespace App\Http\Requests\Outcome;

use App\Models\Outcome;
use Illuminate\Foundation\Http\FormRequest;

class RecordOutcomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $outcome = $this->route('outcome');

        if (! $outcome instanceof Outcome) {
            return false;
        }

        $user = $this->user();

        return $user?->isAdmin()
            || $outcome->influencer_id === $user?->id
            || $outcome->campaign->brand_id === $user?->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'final_post_url' => ['required', 'url', 'max:2048'],
            'reach' => ['nullable', 'integer', 'min:0'],
            'engagement' => ['nullable', 'integer', 'min:0'],
            'conversions' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
