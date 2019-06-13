<?php

namespace App\Http\Controllers;

use App\AnotherClasses\Builders\DriverInfo;
use App\AnotherClasses\TaximeterConnector;
use App\Http\Requests\UserEditRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRecoveryRequest;
use App\Http\Requests\UserTaximeterRegisterRequest;
use App\Jobs\SendRegistrationSms;
use App\User;
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

    public function getAuthenticatedUser()
    {
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

        $user_phone_number = UserJWT::where('id', $user_id)->first()->phone_number;

        $user = UserJWT::where('id', $user_id)
            ->update([
                'phone_number' => $request->get('phone_number'),
                'name' => $request->get('name'),
                'patronymic' => $request->get('patronymic'),
                'surname' => $request->get('surname')
            ]);

        if ($user_phone_number != $request->get('phone_number')) {
            $code = (string)(rand(100000, 999999));

            UserJWT::where('id', $user_id)
                ->update(['password' => bcrypt($code)]);

            $regSms = new SendRegistrationSms($request->get('phone_number'), $code);
            $this->dispatch($regSms);

            JWTAuth::invalidate(JWTAuth::getToken());

            return ResponseHandler::getJsonResponse(228, "Данные успешно сохранены, номер телефона изменен", compact('user', 'token'));
        }
        return ResponseHandler::getJsonResponse(200, "Данные успешно сохранены", compact('user', 'token'));
    }

    public function taximetr() {
        $user_id = JWTAuth::parseToken()->authenticate()->id;
        $user = UserJWT::where('id', $user_id)
            ->first();
        return TaximeterConnector::getBalance($user->phone_number);
    }
}
