<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EditRequest extends Model
{
    protected $fillable = [
        'phone_number', 'status_id', 'content'
    ];

    public function status()
    {
        return $this->hasOne('App\WithdrawalStatus', 'id', 'status_id');
    }

}
