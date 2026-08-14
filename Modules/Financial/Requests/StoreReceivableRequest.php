<?php

declare(strict_types=1);

namespace Modules\Financial\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReceivableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('receivables.create');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'cost_center_id' => ['nullable', 'integer', 'exists:cost_centers,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'due_date' => ['required', 'date'],
        ];
    }
}
