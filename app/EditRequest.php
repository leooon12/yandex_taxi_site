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

    public function setPhoneNumberAttribute($value)
    {
        $this->attributes['phone_number'] = '8'.$value;
    }

    public function user()
    {
        return $this->hasOne('\TCG\Voyager\Models\User', 'phone_number', 'phone_number')->select(['id', 'surname', 'name', 'patronymic', 'phone_number']);
    }
}
