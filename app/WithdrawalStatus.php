<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WithdrawalStatus extends Model
{
    const WAITING_FOR_CONFIRMATION = 1;
    const COMPLETED = 2;
    const CANCELED = 3;
    const IN_WORK = 4;

    protected $fillable = [
        'name'
    ];
}
