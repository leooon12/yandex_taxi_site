<?php

namespace App\Traits;

trait WithdrawalTrait {

    public function status()
    {
        return $this->hasOne('App\WithdrawalStatus', 'id', 'status_id');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id')->select(['id', 'surname', 'name', 'patronymic', 'phone_number']);
    }

}