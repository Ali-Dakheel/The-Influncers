<?php

namespace App\Http\Requests\Creator;

use App\Enums\CampaignFormat;
use App\Enums\Platform;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePricePackageRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.platform' => ['required', Rule::in(Platform::values())],
            'items.*.format' => ['required', Rule::in(CampaignFormat::values())],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'discount_pct' => ['nullable', 'integer', 'min:0', 'max:99'],
            'total_cents' => ['required', 'integer', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
        ];
    }
}
