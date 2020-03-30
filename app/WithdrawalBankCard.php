<?php

namespace App;

use App\Traits\WithdrawalTrait;
use Illuminate\Database\Eloquent\Model;

class WithdrawalBankCard extends Model
{
    use WithdrawalTrait;

    const COMMISSION = 35;
    const MAX_SUM    = 5;

    protected $fillable = [
        'user_id', 'card_number', 'sum', 'status_id'
    ];
}
