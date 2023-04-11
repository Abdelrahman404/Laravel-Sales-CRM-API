<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateClientRequest extends FormRequest
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
            'id' => 'required',
            'phone' => 'nullable',
            'date' => 'required',
            'name' => 'required',
            'email' => 'nullable|email',
            'address' => 'nullable',
            'google_map' => 'required',
            'country_id' => 'required|exists:countries,id',
            'city_id' =>'nullable|exists:cities,id',
            'area_id' =>'nullable|exists:areas,id',
            'products_interest' => 'nullable',
            'company_level' => 'nullable',
            'company_size' => 'nullable',
            'status' => 'nullable',
            'note' => 'nullable',
        ];
    }

    public function failedValidation(Validator $validator) { 
        //write your bussiness logic here otherwise it will give same old JSON response
       throw new HttpResponseException(sendError($validator->errors())); 
   }
}
