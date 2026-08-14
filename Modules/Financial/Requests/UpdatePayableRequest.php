<?php

declare(strict_types=1);

namespace Modules\Financial\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePayableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('payables.update');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => ['sometimes', 'integer', 'exists:suppliers,id'],
            'purchase_order_id' => ['nullable', 'integer', 'exists:purchase_orders,id'],
            'account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'cost_center_id' => ['nullable', 'integer', 'exists:cost_centers,id'],
            'description' => ['nullable', 'string', 'max:255'],
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'due_date' => ['sometimes', 'date'],
        ];
    }
}
