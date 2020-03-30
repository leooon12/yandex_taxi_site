<?php

namespace App\Http\Controllers;

use App\AnotherClasses\ResponseHandler;
use App\Http\Requests\WithdrawalStatusRequest;
use App\TopUpWithdrawal;
use App\WithdrawalBankAccount;
use App\WithdrawalBankCard;
use App\WithdrawalStatus;
use App\WithdrawalYandex;
use Illuminate\Http\Request;

class AdminPanelWithdrawalController extends Controller
{
    public function index() {
        return view('/vendor/voyager/withdrawal');
    }

    public function get_withdrawals($type = null)
    {
        $models = [WithdrawalBankAccount::class, WithdrawalYandex::class, WithdrawalBankCard::class];
        $withdrawals = [];

        for ($i = 0, $size = count($models); $i < $size; ++$i) {
            $query = $models[$i]::with('status')
                ->with('user');

            if ($type == "in_work")
                $query->where('status_id', WithdrawalStatus::INWORK);

            $result = $query->get();

            foreach ($result as $element) {
                $element->type = explode('\\', $models[$i])[1];
                array_push($withdrawals, $element);
            }
        };

        usort($withdrawals, function($a, $b)
        {
            return strcmp($a->created_at, $b->created_at);
        });

        return $withdrawals;
    }

    public function change_status(WithdrawalStatusRequest $request) {
        $model = "App\\".$request->model_name;

        return $model::where('id', $request->withdrawal_id)
            ->update(['status_id' => $request->status_id]);
    }

    public function get_all_statuses() {
        return WithdrawalStatus::take(100)->get();
    }

    public function top_up_withdrawal(Request $request)
    {
        $withdrawal_info = WithdrawalBankCard::find($request->withdrawal_id);
        $sum = $withdrawal_info->sum - WithdrawalBankCard::COMMISSION;

        if ($sum < 0)
            return ResponseHandler::getJsonResponse(400, "Сумма выплаты с учетом комиссии составляет меньше рубля");

        if ($sum > WithdrawalBankCard::MAX_SUM)
            return ResponseHandler::getJsonResponse(400, "Сумма автовыплаты не может быть больше ".WithdrawalBankCard::MAX_SUM." рублей");

        $result = TopUpController::makePayment($withdrawal_info->card_number, $sum);

        if ($result['status'] == 0) {
            $withdrawal_info->update(['status_id' => WithdrawalStatus::COMPLETED]);

            TopUpWithdrawal::create([
                'transaction_number' => $result['payment']['transaction_number'],
                'card_number' => $withdrawal_info->card_number,
                'sum' => $sum,
                'status' => $result['payment']['status'],
                'withdrawal_bank_card_id' => $withdrawal_info->id,
            ]);

            return ResponseHandler::getJsonResponse(200, "Автовыплата успешно начата");
        }
        else 
            return ResponseHandler::getJsonResponse(400, "Произошла ошибка, попробуйте еще раз");

    }

    public function get_top_up_withdrawal() {
        $topUps = TopUpWithdrawal::orderBy('created_at', 'desc')->paginate(25);

        return view('/vendor/voyager/top_up_withdrawal', ['topUps' => $topUps]);
    }
}
