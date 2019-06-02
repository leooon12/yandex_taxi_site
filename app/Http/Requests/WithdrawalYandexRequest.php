<?php

namespace App\Http\Requests;

use App\AnotherClasses\ResponseHandler;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class WithdrawalYandexRequest extends FormRequest
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
            'yandex_number' => 'required|string',
            'sum'           => 'required|integer'
        ];
    }

    public function messages ()
    {
        return [
            'yandex_number.required'        => 'Не был передан параметр номер счета яндекс',
            'yandex_number.string'          => 'Неверный формат номера счета яндекс',
            'sum.required'                  => 'Не был передан параметр сумма вывода',
            'sum.integer'                   => 'Неверный формат суммы вывода',
        ];
    }

    protected function failedValidation(Validator $validator) {
        ResponseHandler::getValidationResponse(400, $validator->errors());
    }
}
