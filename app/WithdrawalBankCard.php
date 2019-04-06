<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WithdrawalBankCard extends Model
{
    protected $fillable = [
        'user_id', 'card_number', 'sum', 'status_id'
    ];

    public function status()
    {
        return $this->hasOne('App\WithdrawalStatus', 'id', 'status_id');
    }
}
