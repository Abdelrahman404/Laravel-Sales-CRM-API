<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

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
            'username' => 'required|string|unique:users,username,' . $this->input('id'),
            'id' => 'required|exists:users,id',
            'image' => 'nullable|string',
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
