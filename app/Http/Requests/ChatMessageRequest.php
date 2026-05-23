<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'session_id' => ['nullable', 'string', 'uuid'],
            'message' => ['required', 'string', 'min:1', 'max:2000'],
            'channel' => ['nullable', 'string', 'in:web,whatsapp,api'],
        ];
    }
}
