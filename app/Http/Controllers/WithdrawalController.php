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

        if ($this->checkOldWithdrawal($user_id))
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

        if ($this->checkOldWithdrawal($user_id))
            return ResponseHandler::getJsonResponse(400, "У вас уже находится заявка в обработке");

        $model::create([
            "user_id"     => $user_id,
            $field_name => $number,
            "sum"         => $sum
        ]);

        return ResponseHandler::getJsonResponse(200, "Заявка на вывод средств успешно отправлена");
    }

    public function checkOldWithdrawal($user_id) {
        $models = [WithdrawalBankAccount::class, WithdrawalYandex::class, WithdrawalBankCard::class];

        for ($i = 0, $size = count($models); $i < $size; ++$i) {
            $result =
                $models[$i]::where("user_id", $user_id)
                    ->where("status_id", WithdrawalStatus::INWORK)
                    ->first();

            if ($result)
                break;

        };

        return $result;
    }

    public function getLastWithdrawal() {
        $user_id = JWTAuth::parseToken()->authenticate()->id;

        $models = [WithdrawalBankAccount::class, WithdrawalYandex::class, WithdrawalBankCard::class];

        $lastWithdrawal = null;

        for ($i = 0, $size = count($models); $i < $size; ++$i) {
            $lastModelWithdrawal =
                $models[$i]::where("user_id", $user_id)
                    ->orderBy('created_at', 'desc')
                    ->with('status')
                    ->first();

            if (!is_null($lastWithdrawal)) {
                if (!is_null($lastModelWithdrawal))
                    if ($lastModelWithdrawal->created_at > $lastWithdrawal->created_at)
                        $lastWithdrawal = $lastModelWithdrawal;
            }
            else
                $lastWithdrawal = $lastModelWithdrawal;

        };

        if (!is_null($lastWithdrawal))
            return ResponseHandler::getJsonResponse(200, "Данные успешно получены", $lastWithdrawal->status);

        return ResponseHandler::getJsonResponse(400, "Данных по заявкам не найдено");
    }
}
