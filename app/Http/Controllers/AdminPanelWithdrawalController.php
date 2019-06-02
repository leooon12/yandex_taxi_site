<?php

namespace App\Http\Controllers;

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

    public function change_status($id, $status_id) {

    }
}
