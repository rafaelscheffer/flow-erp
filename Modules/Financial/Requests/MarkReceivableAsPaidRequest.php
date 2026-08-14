<?php

declare(strict_types=1);

namespace Modules\Financial\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Financial\Enums\PaymentMethod;

class MarkReceivableAsPaidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('receivables.mark-paid');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', 'in:'.implode(',', array_column(PaymentMethod::cases(), 'value'))],
        ];
    }
}
