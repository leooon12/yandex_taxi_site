<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\UserJWT;
use Illuminate\Http\Request;
use JWTAuth;
use App\AnotherClasses\ResponseHandler;

class UserController extends Controller
{

    public function register(UserRegisterRequest $request)
    {
        $user = UserJWT::create([
            'name' => $request->get('name'),
            'phone_number' => $request->get('phone_number'),
            'password' => bcrypt('000000'),
        ]);

        $token = JWTAuth::fromUser($user);

        return ResponseHandler::getJsonResponse(200, "Вы успешно зарегистрированы, ожидайте смс-пароля.", compact('user', 'token'));
    }
}
