<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'category' => ['required', 'string', 'max:80'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
            'keywords' => ['nullable', 'array'],
            'keywords.*' => ['string', 'max:60'],
            'is_active' => ['boolean'],
            'priority' => ['integer', 'min:0', 'max:1000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function prepareForValidation(): array
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
            'priority' => (int) $this->input('priority', 0),
        ]);

        return $this->all();
    }
}
