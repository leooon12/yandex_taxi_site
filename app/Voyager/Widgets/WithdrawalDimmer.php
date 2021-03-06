<?php

namespace App\Voyager\Widgets;

use App\WithdrawalBankAccount;
use App\WithdrawalBankCard;
use App\WithdrawalStatus;
use App\WithdrawalYandex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Widgets\BaseDimmer;

class WithdrawalDimmer extends BaseDimmer
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        $bank_card_count    = WithdrawalBankCard::where('status_id', WithdrawalStatus::WAITING_FOR_CONFIRMATION)->count();
        $bank_account_count = WithdrawalBankAccount::where('status_id', WithdrawalStatus::WAITING_FOR_CONFIRMATION)->count();
        $yandex_count       = WithdrawalYandex::where('status_id', WithdrawalStatus::WAITING_FOR_CONFIRMATION)->count();

        $count = $bank_card_count + $bank_account_count + $yandex_count;

        $string = 'Заявки на выплату';

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-dollar',
            'title'  => " {$string}",
            'text'   => __('voyager::dimmer.user_text', ['count' => $count, 'string' => 'новых заявок']),
            'button' => [
                'text' => 'Открыть',
                'link' => route('voyager.withdrawal.index'),
            ],
            'image' => ('images/voyager/widgets/01.jpg'),
        ]));
    }

    /**
     * Determine if the widget should be displayed.
     *
     * @return bool
     */
    public function shouldBeDisplayed()
    {
        return Auth::user()->can('browse', Voyager::model('User'));
    }
}
