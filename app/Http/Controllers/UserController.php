<?php

namespace App\Http\Controllers;

use App\AnotherClasses\TaximeterParser;
use App\Http\Requests\UserEditRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRecoveryRequest;
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
            'patronymic' => $request->get('patronymic'),
            'surname' => $request->get('patronymic'),
            'phone_number' => $request->get('phone_number'),
            'password' => bcrypt($code),
        ]);

        $regSms = new SendRegistrationSms($request->get('phone_number'), $code);
        $this->dispatch($regSms);

        return ResponseHandler::getJsonResponse(200, "Вы успешно зарегистрированы, ожидайте смс-пароля", compact('user', 'token'));
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
        dd($this->taximetr());

        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return ResponseHandler::getJsonResponse(404, "Пользователь не найден");
        }

        return ResponseHandler::getJsonResponse(200, "данные успешно получены", compact('user'));
    }


    public function recovery(UserRecoveryRequest $request)
    {
        $code = (string)(rand(100000, 999999));

        $user = UserJWT::where('phone_number', $request->get('phone_number'))
            ->update([
            'password' => bcrypt($code),
        ]);

        $regSms = new SendRegistrationSms($request->get('phone_number'), $code);
        $this->dispatch($regSms);

        return ResponseHandler::getJsonResponse(200, "Восстановление пароля успешно, ожидайте смс-пароля", compact('user', 'token'));
    }

    public function edit(UserEditRequest $request) {
        $user_id = JWTAuth::parseToken()->authenticate()->id;

        $user = UserJWT::where('id', $user_id)
            ->update([
                'phone_number' => $request->get('phone_number'),
                'name' => $request->get('name'),
                'patronymic' => $request->get('patronymic'),
                'surname' => $request->get('surname')
            ]);

        return ResponseHandler::getJsonResponse(200, "Данные успешно сохранены", compact('user', 'token'));
    }

    public function taximetr() {
        $user_id = JWTAuth::parseToken()->authenticate()->id;
        $user = UserJWT::where('id', $user_id)
            ->first();
        return TaximeterParser::getBalance($user->phone_number);
    }
}
