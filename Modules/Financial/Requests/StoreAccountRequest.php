<?php

declare(strict_types=1);

namespace Modules\Financial\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Financial\Enums\AccountType;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('accounts.create');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:255', 'unique:accounts,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:'.implode(',', array_column(AccountType::cases(), 'value'))],
            'parent_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
