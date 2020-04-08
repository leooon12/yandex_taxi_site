<?php

namespace App\Providers;

use App\TopUpWithdrawal;
use App\WithdrawalBankCard;
use App\WithdrawalQiwi;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
	    header('Access-Control-Allow-Origin: *');
	    header("Access-Control-Allow-Headers: *");

        Schema::defaultStringLength(191);

        Relation::morphMap([
            TopUpWithdrawal::BANK_CARD_WITHDRAWAL_TYPE => WithdrawalBankCard::class,
            TopUpWithdrawal::QIWI_WITHDRAWAL_TYPE      => WithdrawalQiwi::class
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
