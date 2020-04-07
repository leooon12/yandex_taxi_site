<?php

namespace App;

use App\Traits\WithdrawalTrait;
use Illuminate\Database\Eloquent\Model;

class WithdrawalQiwi extends Model
{
    use WithdrawalTrait;

    protected $fillable = [
        'user_id', 'qiwi_number', 'sum', 'status_id'
    ];
}
