<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Jobs\SendRegistrationSms;
use App\UserJWT;
use Illuminate\Http\Request;
use JWTAuth;
use App\AnotherClasses\ResponseHandler;

class UserController extends Controller
{

    public function register(UserRegisterRequest $request)
    {

        $code = (string)(rand(100000, 999999));

        $user = UserJWT::create([
            'name' => $request->get('name'),
            'phone_number' => $request->get('phone_number'),
            'password' => bcrypt($code),
        ]);

        $token = JWTAuth::fromUser($user);

        $regSms = new SendRegistrationSms($request->get('phone_number'), $code);
        $this->dispatch($regSms);

        return ResponseHandler::getJsonResponse(200, "Вы успешно зарегистрированы, ожидайте смс-пароля.", compact('user', 'token'));
    }
}
