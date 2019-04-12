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
            'name'          => 'required|string|min:3|max:100',
            'patronymic'    => 'required|string|min:3|max:100',
            'surname'       => 'required|string|min:2|max:100',
        ];
    }

    public function messages ()
    {
        return [
            'name.required'             => 'Не был передан параметр имя',
            'name.string'               => 'Неверный формат имени',
            'name.min'                  => 'Длина имени должна быть не меньше 3 символов',
            'name.max'                  => 'Длина имени должна быть не больше 100 символов',
            'patronymic.required'       => 'Не был передан параметр отчество',
            'patronymic.string'         => 'Неверный формат отчества',
            'patronymic.min'            => 'Длина отчества должна быть не меньше 3 символов',
            'patronymic.max'            => 'Длина отчества должна быть не больше 100 символов',
            'surname.required'          => 'Не был передан параметр фамилия',
            'surname.string'            => 'Неверный формат фамилии',
            'surname.min'               => 'Длина фомилии должна быть не меньше 2 символов',
            'surname.max'               => 'Длина фамилии должна быть не больше 100 символов',
            'phone_number.required'     => 'Не был передан параметр номер телефона',
            'phone_number.string'       => 'Неверный формат номера телефона',
            'phone_number.size'         => 'Длина номера телефона должна быть 11 символов',
        ];
    }

    protected function failedValidation(Validator $validator) {
        ResponseHandler::getValidationResponse(400, $validator->errors());
    }
}
