<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminPanelWithdrawalController extends Controller
{
    public function withdrawal()
    {
        return view('/vendor/voyager/withdrawal');
    }
}
