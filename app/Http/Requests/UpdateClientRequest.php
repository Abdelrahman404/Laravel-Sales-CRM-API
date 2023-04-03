<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'id' => 'required:',
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
            'status' => 'nullable',
            'note' => 'nullable',
        ];
    }
}
