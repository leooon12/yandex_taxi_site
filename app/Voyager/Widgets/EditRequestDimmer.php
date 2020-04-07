<?php

namespace App\Voyager\Widgets;

use App\EditRequest;
use App\TopUpWithdrawal;
use App\WithdrawalBankAccount;
use App\WithdrawalBankCard;
use App\WithdrawalStatus;
use App\WithdrawalYandex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Widgets\BaseDimmer;

class EditRequestDimmer extends BaseDimmer
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
        $count = EditRequest::where('status_id', WithdrawalStatus::WAITING_FOR_CONFIRMATION)->count();

        $string = 'Заявки на редактирование данных';

        return view('voyager::dimmer', array_merge($this->config, [
            'icon'   => 'voyager-edit',
            'title'  => " {$string}",
            'text'   => __('voyager::dimmer.user_text', ['count' => $count, 'string' => 'новых заявок']),
            'button' => [
                'text' => 'Открыть',
                'link' => route('voyager.editRequests.index'),
            ],
            'image' => ('images/voyager/widgets/03.jpg'),
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
