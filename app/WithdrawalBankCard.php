<?php

namespace App;

use App\Traits\WithdrawalTrait;
use Illuminate\Database\Eloquent\Model;

class WithdrawalBankCard extends Model
{
    use WithdrawalTrait;

    protected $fillable = [
        'user_id', 'card_number', 'sum', 'status_id'
    ];
}
