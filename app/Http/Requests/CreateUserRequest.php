<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email|unique:users',
            'name_en' => 'required|string|',
            'name_ar' => 'required|string',
            'password'=> 'required|string',
            'type' => 'required|string',
            'details' => 'nullable|array',
            'details.*.country_id' => 'nullable|exists:countries,id',
            'details.*.comission' => 'nullable|string',
            'details.*.target' => 'nullable|string',
        ];
    }
}
