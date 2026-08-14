<?php

declare(strict_types=1);

namespace Modules\Customers\Requests;

use App\Rules\ValidCnpj;
use App\Rules\ValidCpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Customers\Enums\CustomerType;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('customers.update');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $type = $this->input('type', $this->route('customer')->type->value);

        return [
            'type' => ['sometimes', 'string', 'in:'.implode(',', array_column(CustomerType::cases(), 'value'))],
            'name' => ['sometimes', 'string', 'max:255'],
            'trade_name' => ['nullable', 'string', 'max:255'],
            'document' => [
                'sometimes',
                'string',
                'max:14',
                Rule::unique('customers', 'document')->ignore($this->route('customer')),
                $type === CustomerType::Company->value ? new ValidCnpj : new ValidCpf,
            ],
            'state_registration' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'zip_code' => ['nullable', 'string', 'max:8'],
            'address' => ['nullable', 'string', 'max:255'],
            'address_number' => ['nullable', 'string', 'max:20'],
            'address_complement' => ['nullable', 'string', 'max:255'],
            'neighborhood' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:2'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
