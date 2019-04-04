<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WithdrawalBankAccount extends Model
{
    protected $fillable = [
        'user_id', 'account_number', 'patronymic', 'surname', 'name', 'sum', 'status_id'
    ];
}
