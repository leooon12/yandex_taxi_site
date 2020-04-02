<?php

namespace App\Http\Controllers;

use App\AnotherClasses\Api\WithdrawalUtils;
use App\AnotherClasses\ResponseHandler;
use App\Http\Requests\WithdrawalStatusRequest;
use App\Jobs\CheckTopUpWithdrawalJob;
use App\TopUpWithdrawal;
use App\WithdrawalBankAccount;
use App\WithdrawalBankCard;
use App\WithdrawalStatus;
use App\WithdrawalYandex;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use TCG\Voyager\Models\User;

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
                ->take(45)
                ->with('user');

            if ($type == "in_work")
                $query->where('status_id', WithdrawalStatus::WAITING_FOR_CONFIRMATION);

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
        return WithdrawalStatus::get();
    }

    public function top_up_withdrawal(Request $request)
    {
        $withdrawal_bank_card = WithdrawalBankCard::find($request->withdrawal_id);

        if ($withdrawal_bank_card->status_id != WithdrawalStatus::WAITING_FOR_CONFIRMATION)
        {
            return ResponseHandler::getJsonResponse(400, "Автовыплату можно осуществлять только на ожидающие подтверждения запросы");
        }

        //Запрошенная сумма выплаты меньше или равна коммисии, выплату произвести невозможно
        if ($withdrawal_bank_card->sum <= WithdrawalBankCard::COMMISSION)
        {
            return $this->setWithdrawalStatusAndResponse($withdrawal_bank_card, WithdrawalStatus::CANCELED, 400, 'Сумма автовыплаты должна быть больше '.WithdrawalBankCard::COMMISSION.' рублей');
        }

        //Запрошенная сумма выплаты больше максимально возможной
        if ($withdrawal_bank_card-> sum > WithdrawalBankCard::MAX_SUM)
        {
            return $this->setWithdrawalStatusAndResponse($withdrawal_bank_card, WithdrawalStatus::CANCELED, 400, 'Сумма автовыплаты не может быть больше '.WithdrawalBankCard::MAX_SUM.' рублей');
        }

        //Сумма к выплате с учетом комиссии
        $sum_to_pay = $withdrawal_bank_card->sum - WithdrawalBankCard::COMMISSION;

        //Профиль водителя по идентификатору пользователя
        $driver_profile = WithdrawalUtils::getDriverProfile($withdrawal_bank_card->user_id);

        //Профиль водителя найти не удалось
        if ($driver_profile == null)
        {
            return $this->setWithdrawalStatusAndResponse($withdrawal_bank_card, WithdrawalStatus::CANCELED, 400, 'Не удалось найти профиль водителя, запросившего автовыплату');
        }

        //Количество денег проверяется до вычета комиисии
        if (!WithdrawalUtils::isDriverBalanceSufficient($driver_profile, $withdrawal_bank_card->sum))
        {
            return $this->setWithdrawalStatusAndResponse($withdrawal_bank_card, WithdrawalStatus::CANCELED, 400, 'На балансе водителя недостаточно средств для осуществления автовыплаты');
        }

        //С профиля списывается количество денег до вычета комиссии
        if (!WithdrawalUtils::takeFromBalance($driver_profile, $withdrawal_bank_card->sum))
        {
            return $this->setWithdrawalStatusAndResponse($withdrawal_bank_card, WithdrawalStatus::CANCELED, 400, 'На удалось списать требуемую сумму с баланса водителя');
        }

        //Обращение к TopUp API для проведения платежа
        $top_up_response = TopUpController::makePayment($withdrawal_bank_card->card_number, $sum_to_pay);

        //Ошибка ПРОВЕДЕНИЯ платежа (в API)
        if ($top_up_response['status'] != TopUpController::REQUEST_RESULT_SUCCESS)
        {
            //Не удалось вернуть деньги на счет водителя, критическая ошибка
            //TODO: очевидно, что такую ошибку придется обрабатывать вручную, нужно придумать механизм нотификации для этого
            if (!WithdrawalUtils::addToBalance($driver_profile, $withdrawal_bank_card->sum))
            {
                return $this->setWithdrawalStatusAndResponse($withdrawal_bank_card, WithdrawalStatus::CANCELED, 400, 'Произошла ошибка проведения автовыплаты, не удалось вернуть '.$withdrawal_bank_card->sum.' рублей водителю с идентификатором "'.$driver_profile['driver_profile']['id'].'"');
            }

            return $this->setWithdrawalStatusAndResponse($withdrawal_bank_card, WithdrawalStatus::CANCELED, 400, 'Произошла ошибка проведения автовыплаты, попробуйте еще раз');
        }

        //Ошибка проведения платежа
        //TODO: Необходимо подумать на тему отдельной проверки статусов с номерами 40-49 (по описанию API это означает, что необходимо обратиться в поддержку)
        if ($top_up_response['payment']['status'] < TopUpController::PAYMENT_RESULT_MIN_POSSIBLE_VALUE ||
            $top_up_response['payment']['status'] > TopUpController::PAYMENT_RESULT_MAX_POSSIBLE_VALUE)
        {
            //Не удалось вернуть деньги на счет водителя, критическая ошибка
            //TODO: очевидно, что такую ошибку придется обрабатывать вручную, нужно придумать механизм нотификации для этого
            if (!WithdrawalUtils::addToBalance($driver_profile, $withdrawal_bank_card->sum))
            {
                return $this->setWithdrawalStatusAndResponse($withdrawal_bank_card, WithdrawalStatus::CANCELED, 400, 'Не удалось провести выплату, не удалось вернуть '.$withdrawal_bank_card->sum.' рублей водителю с идентификатором "'.$driver_profile['driver_profile']['id'].'"');
            }

            return $this->setWithdrawalStatusAndResponse($withdrawal_bank_card, WithdrawalStatus::CANCELED, 400, 'Не удалось провести выплату, проверьте введенные данные и попробуйте еще раз');
        }

        //Запрос выполнен успешно, выплата в обработке, добавление выплаты в базу данных
        $top_up_withdrawal = TopUpWithdrawal::create
        (
            [
                'transaction_number' => $top_up_response['payment']['transaction_number'],
                'card_number' => $withdrawal_bank_card->card_number,
                'sum' => $sum_to_pay,
                'status' => $top_up_response['payment']['status'],
                'withdrawal_bank_card_id' => $withdrawal_bank_card->id
            ]
        );

        //Выплата проведена успешно
        if ($top_up_response['payment']['status'] == TopUpController::PAYMENT_RESULT_SUCCESS)
        {
            return $this->setWithdrawalStatusAndResponse($withdrawal_bank_card, WithdrawalStatus::COMPLETED, 200, 'Автовыплата успешно выполнена');
        }

        //Выплата в обработке
        $withdrawal_bank_card->update(['status_id' => WithdrawalStatus::IN_WORK]);
        CheckTopUpWithdrawalJob::dispatch($top_up_withdrawal->id)->delay(now()->addMinutes(CheckTopUpWithdrawalJob::DELAY_TIME_IN_MINUTES));

        return ResponseHandler::getJsonResponse(200, "Автовыплата передана в обработку");
    }

    public function get_top_up_withdrawal() {
        $topUps = TopUpWithdrawal::orderBy('created_at', 'desc')->paginate(25);

        return view('/vendor/voyager/top_up_withdrawal', ['topUps' => $topUps]);
    }

    /**
     * Устанавливает статус запроса на выплату и возвращает ответ
     *
     * @param WithdrawalBankCard | WithdrawalBankAccount | WithdrawalYandex $withdrawal Запрос на выплату
     * @param int $withdrawal_status Идентификатор статуса запроса
     * @param int $response_status Статус ответа
     * @param string $message Сообщение ответа
     *
     * @return JsonResponse Ответ на запрос
     */
    private function setWithdrawalStatusAndResponse(&$withdrawal, $withdrawal_status, $response_status, $message)
    {
        $withdrawal->update(['status_id' => $withdrawal_status]);
        return ResponseHandler::getJsonResponse($response_status, $message);
    }
}
