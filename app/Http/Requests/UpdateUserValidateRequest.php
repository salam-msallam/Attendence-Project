<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserValidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'=>'sometimes|required|string',
            'last_name'=>'sometimes|required|string',
            'Phone'=>'sometimes|required',
            'email'=>'sometimes|required',
            'password'=>'sometimes|required',
            'role'=>'sometimes|required'
        ];
    }
}
