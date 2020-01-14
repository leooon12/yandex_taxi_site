<?php

namespace App\Http\Requests;

use App\AnotherClasses\ResponseHandler;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class DriverDataRequest extends FormRequest
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
            'full_name'         => 'required|string|min:5|max:100',
            'phone_number'      => 'required|string|size:11'
        ];
    }

    public function messages ()
    {
        return [
            'full_name.required'        => 'Не указано: ФИО',
            'full_name.string'          => 'Неверный формат ФИО',
            'full_name.min'             => 'Длина ФИО должна быть не меньше 5 символов',
            'full_name.max'             => 'Длина ФИО должна быть не больше 100 символов',
            'phone_number.required'     => 'Не указано: номер телефона',
            'phone_number.string'       => 'Неверный формат номера телефона',
            'phone_number.size'         => 'Длина номера телефона должна быть 11 символов'
        ];
    }

    protected function failedValidation(Validator $validator) {
        ResponseHandler::getValidationResponse(400, $validator->errors());
    }
}
