<?php

namespace App\Http\Requests;

class TenantRequest extends BaseFormRequest
{

    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:tenants,name',


            'db_name' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9_]+$/',
                'max:64',
                'unique:tenants,db_name',
            ],
            'db_user' => [
                'required',
                'string',
                'max:64',
            ],
            'db_pass' => [
                'required',
                'string',
                'min:8',
                'max:128',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[@$!%*?&])[A-Za-z\\d@$!%*?&]{8,}$/'
            ],
        ];
    }
}
