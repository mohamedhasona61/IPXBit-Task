<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseFormRequest;

class CreateTenantRequest extends BaseFormRequest
{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name'    => 'required|string|max:255',
            'slug'    => 'required|string|max:255|unique:tenants,slug',
            'db_name' => 'required|string|max:255|unique:tenants,db_name',
            'db_user' => 'required|string|max:255',
            'db_pass' => 'required|string|max:255',
        ];
    }
}
