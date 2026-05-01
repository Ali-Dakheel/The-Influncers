<?php

namespace App\Http\Requests\Creator;

use App\Enums\CampaignFormat;
use App\Enums\Platform;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetPriceListItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && ($user->isInfluencer() || $user->isAgency() || $user->isAdmin());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'platform' => ['required', Rule::in(Platform::values())],
            'format' => ['required', Rule::in(CampaignFormat::values())],
            'price_cents' => ['required', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
        ];
    }
}
