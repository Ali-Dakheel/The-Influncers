<?php

namespace App\Http\Requests\Draft;

use App\Enums\Platform;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitDraftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('submitDraft', $this->route('application')) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'platform' => ['required', Rule::in(Platform::values())],
            'caption' => ['nullable', 'string', 'max:5000'],
            'file_url' => ['required_without:file_path', 'nullable', 'url', 'max:2048'],
            'file_path' => ['required_without:file_url', 'nullable', 'string', 'max:2048'],
        ];
    }
}
