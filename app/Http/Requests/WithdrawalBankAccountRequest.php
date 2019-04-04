<?php

namespace App\Http\Requests;

use App\AnotherClasses\ResponseHandler;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class WithdrawalBankAccountRequest extends FormRequest
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
            'account_number'    => 'required|string|size:16',
            'surname'           => 'required|string',
            'patronymic'        => 'required|string',
            'name'              => 'required|string',
            'sum'               => 'required|integer'
        ];
    }

    public function messages ()
    {
        return [
            'account_number.required'   => 'Не был передан параметр номер банковской карты',
            'account_number.string'     => 'Неверный формат банковской карты',
            'surname.required'          => 'Не был передан параметр фамилия',
            'surname.string'            => 'Неверный формат фамилии',
            'patronymic.required'       => 'Не был передан параметр отчество',
            'patronymic.string'         => 'Неверный формат отчества',
            'name.required'             => 'Не был передан параметр имя',
            'name.string'               => 'Неверный формат имени',
            'sum.required'              => 'Не был передан параметр сумма вывода',
            'sum.integer'               => 'Неверный формат суммы вывода',
        ];
    }

    protected function failedValidation(Validator $validator) {
        ResponseHandler::getValidationResponse(400, $validator->errors());
    }
}
