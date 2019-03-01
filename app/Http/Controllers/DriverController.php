<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverDataRequest;
use App\Mail\DriverRequestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class DriverController extends Controller
{
    public function index()
    {
        return response()->json(['status' => '200', 'message' => 'Данные получены']);
    }

    public function store(DriverDataRequest $request)
    {
        Mail::to("develeone@yandex.ru")->send(new DriverRequestMail($request->full_name, $request->phone_number));

        return response()->json(['status' => '200', 'message' => "Ваша заявка успешно зарегистрирована!"]);
    }
}
