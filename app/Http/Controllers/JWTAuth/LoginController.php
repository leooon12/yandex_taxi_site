<?php
/**
 * Created by PhpStorm.
 * User: fireeagle
 * Date: 12.06.19
 * Time: 23:02
 */

namespace App\Http\Controllers\JWTAuth;


use App\AnotherClasses\ResponseHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
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
}