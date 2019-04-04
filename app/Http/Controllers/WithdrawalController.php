<?php

namespace App\Http\Controllers;

use App\AnotherClasses\ResponseHandler;
use App\Http\Requests\WithdrawalBankCardRequest;
use App\Http\Requests\WithdrawalYandexRequest;
use App\WithdrawalBankCard;
use App\WithdrawalStatus;
use App\WithdrawalYandex;
use Illuminate\Http\Request;
use JWTAuth;

class WithdrawalController extends Controller
{
    public function withdrawalBankCard(WithdrawalBankCardRequest $request)
    {
        return $this->withdrawal(
            WithdrawalBankCard::class,
            JWTAuth::parseToken()->authenticate()->id,
            $request->get("sum"),
            $request->get("card_number"),
            "card_number"
        );
    }

    public function withdrawalYandex(WithdrawalBankCardRequest $request)
    {
        return $this->withdrawal(
            WithdrawalYandex::class,
            JWTAuth::parseToken()->authenticate()->id,
            $request->get("sum"),
            $request->get("card_number"),
            "yandex_number"
        );
    }


    public function withdrawal($model, $user_id, $sum, $number, $field_name) {

        $check_old_withdrawal =
            $model::where("user_id", $user_id)
                ->where("status_id", WithdrawalStatus::INWORK)
                ->first();

        if ($check_old_withdrawal)
            return ResponseHandler::getJsonResponse(400, "У вас уже находится заявка в обработке");

        $model::create([
            "user_id"     => $user_id,
            $field_name => $number,
            "sum"         => $sum
        ]);

        return ResponseHandler::getJsonResponse(200, "Заявка на вывод средств успешно отправлена");
    }
}
