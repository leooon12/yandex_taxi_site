<?php

namespace App;

use App\Traits\WithdrawalTrait;
use Illuminate\Database\Eloquent\Model;

class WithdrawalBankCard extends Model
{
    use WithdrawalTrait;

    const COMMISSION = 35;
    const MAX_SUM    = 2000;

    protected $fillable = [
        'user_id', 'card_number', 'sum', 'status_id'
    ];

    public function topUp()
    {
        return $this->morphMany('App\TopUpWithdrawal', 'withdrawal');
    }
}
