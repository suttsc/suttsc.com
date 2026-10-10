<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\LinkCategory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

final class StoreLinkRequest extends FormRequest
{
    /**
     * @return array<string, list<ValidationRule|Enum|string>>
     */
    public function rules(): array
    {
        return [
            'url' => ['required', 'string', 'max:2048', 'url:http,https'],
            'category' => ['required', Rule::enum(LinkCategory::class)],
            'reason' => ['required', 'string', 'max:1000'],
            'author' => ['nullable', 'string', 'max:255'],
        ];
    }
}
