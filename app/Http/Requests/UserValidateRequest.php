<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserValidateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'=>'required|string',
            'last_name'=>'required|string',
            'gender'=>'required',
            'Phone'=>'required',
            'year'=>'required',
            'specialization'=>'required',
            'email'=>'required',
            'password'=>'required',
            'role'=>'required'
        ];
    }
}
