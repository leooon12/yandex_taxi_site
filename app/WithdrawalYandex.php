<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WithdrawalYandex extends Model
{
    protected $fillable = [
        'user_id', 'yandex_number', 'sum', 'status_id'
    ];

    public function status()
    {
        return $this->hasOne('App\WithdrawalStatus', 'id', 'status_id');
    }
}
