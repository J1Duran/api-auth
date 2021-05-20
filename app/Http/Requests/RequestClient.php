<?php

namespace App\Http\Requests;

use App\Banco;
use Illuminate\Foundation\Http\FormRequest;

class RequestClient extends FormRequest
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
            'name' => 'required|max:255',
            'redirect' => 'required|url',
        ];
    }

    /**
     * Configure the validator instance.
     * validacion adicionales
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {   
        
        // $validator->after(function ($validator) {
            
            
        // });
    }
}
