<?php

declare(strict_types=1);

namespace Modules\Sales\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Sales\Enums\PaymentMethod;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('orders.update');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['sometimes', 'integer', 'exists:customers,id'],
            'stock_location_id' => ['sometimes', 'integer', 'exists:stock_locations,id'],
            'order_date' => ['nullable', 'date'],
            'payment_method' => ['sometimes', 'string', 'in:'.implode(',', array_column(PaymentMethod::cases(), 'value'))],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'shipping_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.product_id' => ['required_with:items', 'integer', 'exists:products,id'],
            'items.*.product_variant_id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
        ];
    }
}
