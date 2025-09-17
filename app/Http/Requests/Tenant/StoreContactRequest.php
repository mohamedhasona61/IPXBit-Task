<?php

namespace App\Http\Requests\Tenant;

use App\Http\Requests\BaseFormRequest;

class StoreContactRequest extends BaseFormRequest
{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:15',
            'notes' => 'nullable|string',
        ];
    }
}
