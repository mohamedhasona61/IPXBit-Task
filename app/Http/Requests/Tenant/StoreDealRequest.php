<?php

namespace App\Http\Requests\Tenant;

use App\Http\Requests\BaseFormRequest;

class StoreDealRequest extends BaseFormRequest
{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'title'      => 'required|string|max:255',
            'amount'     => 'required|numeric|min:0',
            'status'     => 'required|in:open,won,lost',
            'contact_id' => 'nullable|exists:tenant.contacts,id',
        ];
    }
}
