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
            'document_country'          => 'required|string'
        ];
    }

    public function messages ()
    {
        return [
            'name.required'                                 => 'Не указано: имя',
            'name.string'                                   => 'Неверный формат имени',
            'name.min'                                      => 'Длина имени должна быть не меньше 2 символов',
            'name.max'                                      => 'Длина имени должна быть не больше 100 символов',
            'surname.required'                              => 'Не указано: фамилия',
            'surname.string'                                => 'Неверный формат фамилии',
            'surname.min'                                   => 'Длина фамилии должна быть не меньше 2 символов',
            'surname.max'                                   => 'Длина фамилии должна быть не больше 100 символов',
            'patronymic.required'                           => 'Не указано: отчество',
            'patronymic.string'                             => 'Неверный формат отчества',
            'patronymic.max'                                => 'Длина отчества должна быть не больше 100 символов',
            'document_serial_number.required'               => 'Не указано: серия прав',
            'document_uniq_number.required'                 => 'Не указано: номер прав',
            'document_issue_date.required'                  => 'Не указано: дата выдачи прав',
            'document_end_date.required'                    => 'Не указано: дата окончания действия прав',
            'document_country.required'                     => 'Не указано: страна, выдавшая права',
            'document_serial_number.string'                 => 'Неверный формат параметра: серия прав',
            'document_uniq_number.string'                   => 'Неверный формат параметра: номер прав',
            'document_issue_date.string'                    => 'Неверный формат параметра: дата выдачи прав',
            'document_end_date.string'                      => 'Неверный формат параметра: дата окончания действия прав',
            'document_country.string'                       => 'Неверный формат параметра: страна, выдавшая права'
        ];
    }

    protected function failedValidation(Validator $validator) {
        ResponseHandler::getValidationResponse(400, $validator->errors());
    }
}
