<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopUpWithdrawal extends Model
{
    protected $fillable = ['transaction_number', 'card_number', 'sum', 'status', 'withdrawal_bank_card_id'];
}
