<?php

namespace App;

use App\Traits\WithdrawalTrait;
use Illuminate\Database\Eloquent\Model;

class WithdrawalYandex extends Model
{
    use WithdrawalTrait;

    protected $fillable = [
        'user_id', 'yandex_number', 'sum', 'status_id'
    ];

}
