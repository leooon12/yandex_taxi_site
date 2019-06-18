<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\AnotherClasses\ResponseHandler;
use Illuminate\Contracts\Validation\Validator;

class ChangeDriverCarRequest extends FormRequest
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
            'car_brand'                 => 'required|string',
            'car_model'                 => 'required|string',
            'car_creation_year'         => 'required|string',
            'car_color'                 => 'required|string',
            'car_gov_number'            => 'required|string',
            'car_reg_sertificate'       => 'required|string'
        ];
    }

    public function messages ()
    {
        return [
            'car_brand.required'                            => 'Не указано: марка автомобиля',
            'car_model.required'                            => 'Не указано: модель автомобиля',
            'car_creation_year.required'                    => 'Не указано: дата выдачи автомобиля',
            'car_color.required'                            => 'Не указано: цвет автомобиля',
            'car_gov_number.required'                       => 'Не указано: госномер',
            'car_reg_sertificate.required'                  => 'Не указано: серия/номер СТС',
            'car_brand.string'                              => 'Неверный формат параметра: марка автомобиля',
            'car_model.string'                              => 'Неверный формат параметра: модель автомобиля',
            'car_creation_year.string'                      => 'Неверный формат параметра: дата выдачи автомобиля',
            'car_color.string'                              => 'Неверный формат параметра: цвет автомобиля',
            'car_gov_number.string'                         => 'Неверный формат параметра: госномер',
            'car_reg_sertificate.string'                    => 'Неверный формат параметра: серия/номер СТС',
        ];
    }

    protected function failedValidation(Validator $validator) {
        ResponseHandler::getValidationResponse(400, $validator->errors());
    }
}
