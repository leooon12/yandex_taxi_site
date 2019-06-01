<?php

namespace App\Http\Controllers;

use App\AnotherClasses\TaximeterConnector;
use App\Http\Requests\DriverDataRequest;
use App\Mail\DriverRequestMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\AnotherClasses\ResponseHandler;
use JWTAuth;

class DriverController extends Controller
{
    public function index()
    {
        return response()->json(['status' => '200', 'message' => 'Данные получены']);
    }

    public function store(DriverDataRequest $request)
    {
        Mail::to("parkdriver@yandex.ru")->send(new DriverRequestMail($request->full_name, $request->phone_number));

        return ResponseHandler::getJsonResponse(200, 'Ваша заявка успешно зарегистрирована.');
    }

    public function getDriver() {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return ResponseHandler::getJsonResponse(404, "Пользователь не найден");
        }

        $taximeter_user_data = TaximeterConnector::getDriverProfile($user->phone_number);

        return ResponseHandler::getJsonResponse(200, "данные успешно получены", compact('taximeter_user_data'));
    }
}
