<?php

namespace App\Http\Requests;

use App\AnotherClasses\ResponseHandler;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class WithdrawalBankCardRequest extends FormRequest
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
            'card_number'     => 'required|string|size:16',
            'sum'           => 'required|integer'
        ];
    }

    public function messages ()
    {
        return [
            'card_number.required'        => 'Не был передан параметр номер банковской карты',
            'card_number.string'          => 'Неверный формат банковской карты',
            'card_number.size'            => 'Длина номера банковской карты должна быть 16 символов',
            'sum.required'              => 'Не был передан параметр сумма вывода',
            'sum.integer'                => 'Неверный формат суммы вывода',
        ];
    }

    protected function failedValidation(Validator $validator) {
        ResponseHandler::getValidationResponse(400, $validator->errors());
    }
}
