<?php

namespace App\Http\Requests;

use App\AnotherClasses\ResponseHandler;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UserEditRequest extends FormRequest
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
            'name'          => 'required|string|min:5|max:100',
        ];
    }

    public function messages ()
    {
        return [
            'name.required'        => 'Не был передан параметр ФИО',
            'name.string'          => 'Неверный формат ФИО',
            'name.min'             => 'Длина ФИО должна быть не меньше 5 символов',
            'name.max'             => 'Длина ФИО должна быть не больше 100 символов',
            'phone_number.required'     => 'Не был передан параметр номер телефона',
            'phone_number.string'       => 'Неверный формат номера телефона',
            'phone_number.size'         => 'Длина номера телефона должна быть 11 символов',
        ];
    }

    protected function failedValidation(Validator $validator) {
        ResponseHandler::getValidationResponse(400, $validator->errors());
    }
}
