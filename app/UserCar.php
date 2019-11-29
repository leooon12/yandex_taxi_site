<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCar extends Model
{
    protected  $fillable = ['user_id',  'user_taximeter_id', 'car_taximeter_id', 'car_model', 'car_gov_number', 'car_color', 'car_vin', 'car_creation_year', 'car_reg_sertificate'];
}
