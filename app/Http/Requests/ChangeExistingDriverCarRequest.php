<?php

namespace App\Http\Requests;

use App\AnotherClasses\ResponseHandler;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ChangeExistingDriverCarRequest extends FormRequest
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
            'car_id' => 'required|string|exists:user_cars,id',
        ];
    }


    public function messages ()
    {
        return [
            'car_id.required'   => 'Не указано: id автомобиля',
            'car_id.string'     => 'Неверный формат параметра: id автомобиля',
            'car_id.exists'     => 'Данный авто не зарегистрирован в системе',
        ];
    }

    protected function failedValidation(Validator $validator) {
        ResponseHandler::getValidationResponse(400, $validator->errors());
    }
}
