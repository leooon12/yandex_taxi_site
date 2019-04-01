<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Jobs\SendRegistrationSms;
use App\UserJWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use JWTAuth;
use App\AnotherClasses\ResponseHandler;

class UserController extends Controller
{

    function __construct()
    {
        Config::set('auth.providers', ['users' => [
            'driver' => 'eloquent',
            'model' => UserJWT::class,
        ]]);
    }

    public function register(UserRegisterRequest $request)
    {

        $code = (string)(rand(100000, 999999));

        $user = UserJWT::create([
            'name' => $request->get('name'),
            'phone_number' => $request->get('phone_number'),
            'password' => bcrypt($code),
        ]);

        $regSms = new SendRegistrationSms($request->get('phone_number'), $code);
        $this->dispatch($regSms);

        return ResponseHandler::getJsonResponse(200, "Вы успешно зарегистрированы, ожидайте смс-пароля.", compact('user', 'token'));
    }

    public function login(UserLoginRequest $request)
    {
        $credentials = $request->only('phone_number', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return ResponseHandler::getJsonResponse(400, "Неверные данные для входа");
            }
        } catch (JWTException $e) {
            return ResponseHandler::getJsonResponse(500, "Внутренняя ошибка сервиса", $e);
        }

        return ResponseHandler::getJsonResponse(200, "Вы успешно авторизованы", compact('token'));
    }

    public function getAuthenticatedUser()
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return ResponseHandler::getJsonResponse(404, "Пользователь не найден");
        }

        return ResponseHandler::getJsonResponse(200, "данные успешно получены", compact('user'));
    }
}
