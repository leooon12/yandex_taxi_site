<?php

namespace App\Http\Requests;

use App\AnotherClasses\ResponseHandler;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UserLoginRequest extends FormRequest
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
            'phone_number'  => 'required|string|size:11',
            'password'      => 'required|string|size:6'
        ];
    }

    public function messages ()
    {
        return [
            'password.required'         => 'Не был передан параметр пароль',
            'password.string'           => 'Неверный формат пароля',
            'password.size'             => 'Длина пароля должна быть 6 символа',
            'phone_number.required'     => 'Не был передан параметр номер телефона',
            'phone_number.string'       => 'Неверный формат номера телефона',
            'phone_number.size'         => 'Длина номера телефона должна быть 11 символов',
        ];
    }

    protected function failedValidation(Validator $validator) {
        ResponseHandler::getValidationResponse(400, $validator->errors());
    }
}
