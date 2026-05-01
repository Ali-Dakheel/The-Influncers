<?php

namespace App\Http\Requests\Campaign;

use App\Models\Application;
use App\Models\Campaign;
use Illuminate\Foundation\Http\FormRequest;

class ApplyToCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Application::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'pitch' => ['required', 'string', 'min:10'],
            'proposed_price_cents' => ['nullable', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $campaign = $this->route('campaign');

            if (! $campaign instanceof Campaign) {
                return;
            }

            if (! $campaign->isPublished()) {
                $validator->errors()->add('campaign', 'Campaign is not accepting applications.');
            }

            $alreadyApplied = Application::where('campaign_id', $campaign->id)
                ->where('influencer_id', $this->user()->id)
                ->exists();

            if ($alreadyApplied) {
                $validator->errors()->add('campaign', 'You already applied to this campaign.');
            }
        });
    }
}
