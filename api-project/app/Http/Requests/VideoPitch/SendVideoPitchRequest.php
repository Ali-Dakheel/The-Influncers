<?php

namespace App\Http\Requests\VideoPitch;

use Illuminate\Foundation\Http\FormRequest;

class SendVideoPitchRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && ($user->isInfluencer() || $user->isAgency());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'brand_id' => ['required', 'exists:users,id'],
            'campaign_id' => ['nullable', 'exists:campaigns,id'],
            'video_url' => ['required', 'url', 'max:2048'],
            'message' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
