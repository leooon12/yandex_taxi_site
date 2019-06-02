<?php

namespace App\Http\Controllers;

use App\AnotherClasses\TaximeterConnector;
use Illuminate\Http\Request;

class CarModelsController extends Controller
{
    public function show($brandName)
    {
        return TaximeterConnector::getCarModels($brandName);
    }
}
