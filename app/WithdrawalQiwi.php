<?php

namespace App;

use App\Traits\WithdrawalTrait;
use Illuminate\Database\Eloquent\Model;

class WithdrawalQiwi extends Model
{
    use WithdrawalTrait;

    const COMMISION = 0;

    protected $fillable = [
        'user_id', 'qiwi_number', 'sum', 'status_id'
    ];

    public function topUp()
    {
        return $this->morphMany('App\TopUpWithdrawal', 'withdrawal');
    }
}
