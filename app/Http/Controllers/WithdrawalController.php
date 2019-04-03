<?php

namespace App\Http\Controllers;

use App\AnotherClasses\ResponseHandler;
use App\Http\Requests\WithdrawalBankCardRequest;
use App\WithdrawalBankCard;
use App\WithdrawalStatus;
use Illuminate\Http\Request;
use JWTAuth;

class WithdrawalController extends Controller
{
    public function withdrawalBankCard(WithdrawalBankCardRequest $request)
    {
        $user_id = JWTAuth::parseToken()->authenticate()->id;
        $check_old_withdrawal =
            WithdrawalBankCard::where("user_id", $user_id)
            ->where("status_id", WithdrawalStatus::INWORK)
            ->first();

        if ($check_old_withdrawal)
            return ResponseHandler::getJsonResponse(400, "У вас уже находится заявка в обработке");

        WithdrawalBankCard::create([
            "user_id"     => $user_id,
            "card_number" => $request->get("card_number"),
            "sum"         => $request->get("sum")
        ]);

        return ResponseHandler::getJsonResponse(200, "Заявка на вывод средств успешно отправлена");
    }
}
