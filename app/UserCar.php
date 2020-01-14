<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCar extends Model
{
    protected  $fillable = ['user_id',  'user_taximeter_id', 'car_taximeter_id', 'car_brand', 'car_model', 'car_gov_number'];
}
