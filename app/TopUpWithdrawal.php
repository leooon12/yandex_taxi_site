<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopUpWithdrawal extends Model
{
    const BANK_CARD_WITHDRAWAL_TYPE    = 'bank_card';
    const QIWI_WITHDRAWAL_TYPE         = 'qiwi';

    protected $fillable = ['transaction_number', 'requisites', 'sum', 'status', 'withdrawal_id', 'withdrawal_type'];

    public function withdrawal()
    {
        return $this->morphTo('withdrawal');
    }
}
