<?php

namespace App\Http\Requests\Creator;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePortfolioRequest extends FormRequest
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
            'bio' => ['nullable', 'string', 'max:5000'],
            'content_style' => ['nullable', 'array'],
            'content_style.*' => ['string', 'max:50'],
            'audience_size' => ['nullable', 'integer', 'min:0'],
            'audience_demographics' => ['nullable', 'array'],
            'past_collabs' => ['nullable', 'array'],
            'past_collabs.*.brand' => ['required_with:past_collabs.*', 'string', 'max:255'],
            'past_collabs.*.year' => ['required_with:past_collabs.*', 'integer', 'min:2000', 'max:2100'],
            'past_collabs.*.deliverables' => ['nullable', 'string', 'max:500'],
            'past_collabs.*.link' => ['nullable', 'url', 'max:2048'],
        ];
    }
}
