<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateClientFormRequest extends FormRequest
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
            'date' => 'required',
            'phone' => 'nullable',
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
            'note' => 'nullable',
        ];
    }

        /**
    * [failedValidation [Overriding the event validator for custom error response]]
    * @param  Validator $validator [description]
    * @return [object][object of various validation errors]
    */
    public function failedValidation(Validator $validator) { 
        //write your bussiness logic here otherwise it will give same old JSON response
       throw new HttpResponseException(sendError($validator->errors())); 
   }



}
