<?php

declare(strict_types=1);

namespace Modules\Financial\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Financial\Enums\AccountType;

class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('accounts.update');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $account = $this->route('account');

        return [
            'code' => ['sometimes', 'string', 'max:255', Rule::unique('accounts', 'code')->ignore($account)],
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'in:'.implode(',', array_column(AccountType::cases(), 'value'))],
            'parent_id' => ['nullable', 'integer', 'exists:accounts,id', Rule::notIn([$account->id, ...$account->descendantIds()])],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
