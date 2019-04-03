<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WithdrawalStatus extends Model
{
    const INWORK = 1;
    const COMPLETED = 2;
    const CANCELED = 3;

    protected $fillable = [
        'name'
    ];
}
