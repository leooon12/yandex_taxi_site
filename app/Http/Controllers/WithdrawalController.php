<?php

namespace App\Http\Controllers;

use App\AnotherClasses\ResponseHandler;
use App\Http\Requests\WithdrawalBankAccountRequest;
use App\Http\Requests\WithdrawalBankCardRequest;
use App\Http\Requests\WithdrawalYandexRequest;
use App\WithdrawalBankAccount;
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

    public function withdrawalYandex(WithdrawalYandexRequest $request)
    {
        return $this->withdrawal(
            WithdrawalYandex::class,
            JWTAuth::parseToken()->authenticate()->id,
            $request->get("sum"),
            $request->get("yandex_number"),
            "yandex_number"
        );
    }

    public function withdrawalBankAccount(WithdrawalBankAccountRequest $request)
    {
        $user_id = JWTAuth::parseToken()->authenticate()->id;

        if ($this->checkOldWithdrawal(WithdrawalBankAccount::class, $user_id))
            return ResponseHandler::getJsonResponse(400, "У вас уже находится заявка в обработке");

        WithdrawalBankAccount::create([
            "user_id"           => $user_id,
            "account_number"    => $request->get("account_number"),
            "sum"               => $request->get("sum"),
            "surname"           => $request->get("surname"),
            "patronymic"        => $request->get("patronymic"),
            "name"              => $request->get("name"),
        ]);

        return ResponseHandler::getJsonResponse(200, "Заявка на вывод средств успешно отправлена");
    }

    public function withdrawal($model, $user_id, $sum, $number, $field_name) {

        if ($this->checkOldWithdrawal($model, $user_id))
            return ResponseHandler::getJsonResponse(400, "У вас уже находится заявка в обработке");

        $model::create([
            "user_id"     => $user_id,
            $field_name => $number,
            "sum"         => $sum
        ]);

        return ResponseHandler::getJsonResponse(200, "Заявка на вывод средств успешно отправлена");
    }

    public function checkOldWithdrawal($model, $user_id) {
        $result =
            $model::where("user_id", $user_id)
                ->where("status_id", WithdrawalStatus::INWORK)
                ->first();

        return $result;
    }
}
