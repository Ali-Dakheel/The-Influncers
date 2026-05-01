<?php

namespace App\Http\Requests\Reputation;

use Illuminate\Foundation\Http\FormRequest;

class RateInfluencerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && ($user->isBrand() || $user->isAdmin());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'score' => ['required', 'integer', 'min:1', 'max:5'],
            'text' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
