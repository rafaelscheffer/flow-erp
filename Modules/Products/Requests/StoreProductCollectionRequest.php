<?php

declare(strict_types=1);

namespace Modules\Products\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('collections.create');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
