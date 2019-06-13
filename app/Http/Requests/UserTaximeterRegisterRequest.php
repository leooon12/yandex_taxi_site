<?php

namespace App\Http\Requests;

use App\AnotherClasses\ResponseHandler;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UserTaximeterRegisterRequest extends FormRequest
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
            'name'                      => 'required|string|min:2|max:100',
            'surname'                   => 'required|string|min:2|max:100',
            'patronymic'                => 'required|string|max:100',
            'document_serial_number'    => 'required|string',
            'document_uniq_number'      => 'required|string',
            'document_issue_date'       => 'required|string',
            'document_end_date'         => 'required|string',
            'document_country'          => 'required|string',
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
            'name.required'                                 => 'Не был передан параметр: имя',
            'name.string'                                   => 'Неверный формат имени',
            'name.min'                                      => 'Длина имени должна быть не меньше 2 символов',
            'name.max'                                      => 'Длина имени должна быть не больше 100 символов',
            'surname.required'                              => 'Не был передан параметр: фамилия',
            'surname.string'                                => 'Неверный формат фамилии',
            'surname.min'                                   => 'Длина фамилии должна быть не меньше 2 символов',
            'surname.max'                                   => 'Длина фамилии должна быть не больше 100 символов',
            'patronymic.required'                           => 'Не был передан параметр: отчество',
            'patronymic.string'                             => 'Неверный формат отчества',
            'patronymic.max'                                => 'Длина отчества должна быть не больше 100 символов',
            'document_serial_number.required'               => 'Не был передан параметр: серия прав',
            'document_uniq_number.required'                 => 'Не был передан параметр: номер прав',
            'document_issue_date.required'                  => 'Не был передан параметр: дата выпуска прав',
            'document_end_date.required'                    => 'Не был передан параметр: дата окончания действия прав',
            'document_country.required'                     => 'Не был передан параметр: страна, выдавшая права',
            'car_brand.required'                            => 'Не был передан параметр: марка автомобиля',
            'car_model.required'                            => 'Не был передан параметр: модель автомобиля',
            'car_creation_year.required'                    => 'Не был передан параметр: дата выпуска автомобиля',
            'car_color.required'                            => 'Не был передан параметр: цвет автомобиля',
            'car_gov_number.required'                       => 'Не был передан параметр: госномер',
            'car_reg_sertificate.required'                  => 'Не был передан параметр: серия/номер СТС',
            'document_serial_number.string'                 => 'Неверный формат параметра: серия прав',
            'document_uniq_number.string'                   => 'Неверный формат параметра: номер прав',
            'document_issue_date.string'                    => 'Неверный формат параметра: дата выпуска прав',
            'document_end_date.string'                      => 'Неверный формат параметра: дата окончания действия прав',
            'document_country.string'                       => 'Неверный формат параметра: страна, выдавшая права',
            'car_brand.string'                              => 'Неверный формат параметра: марка автомобиля',
            'car_model.string'                              => 'Неверный формат параметра: модель автомобиля',
            'car_creation_year.string'                      => 'Неверный формат параметра: дата выпуска автомобиля',
            'car_color.string'                              => 'Неверный формат параметра: цвет автомобиля',
            'car_gov_number.string'                         => 'Неверный формат параметра: госномер',
            'car_reg_sertificate.string'                    => 'Неверный формат параметра: серия/номер СТС',

        ];
    }

    protected function failedValidation(Validator $validator) {
        ResponseHandler::getValidationResponse(400, $validator->errors());
    }
}
