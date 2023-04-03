<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email,' . $this->input('id'),
            'id' => 'required',
            'image' => 'sometimes|mimes:jpeg,jpg,png,gif|max:100000',
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
