<?php

namespace App;

use App\Traits\WithdrawalTrait;
use Illuminate\Database\Eloquent\Model;

class WithdrawalBankAccount extends Model
{
    use WithdrawalTrait;

    protected $fillable = [
        'user_id', 'account_number', 'patronymic', 'surname', 'name', 'sum', 'status_id', 'bik'
    ];

}
