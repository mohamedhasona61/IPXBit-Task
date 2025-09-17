<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;

class SuspendTenantRequest extends BaseFormRequest
{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'status' => 'required|in:suspended,active',
        ];
    }
}
