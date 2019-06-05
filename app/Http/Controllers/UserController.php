<?php

namespace App\Http\Controllers;

use App\AnotherClasses\Builders\DriverBuilder;
use App\AnotherClasses\TaximeterConnector;
use App\Http\Requests\UserEditRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRecoveryRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Jobs\SendRegistrationSms;
use App\User;
use App\UserJWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use JWTAuth;
use App\AnotherClasses\ResponseHandler;

//TODO: РАЗНЕСТИ ЭТУ ПОЕБЕНЬ ПО РАЗНЫМ КОНТРОЛЛЕРАМ: Login, Register, etc

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

        $driverInfo = new DriverBuilder();

        $driverInfo->setName($request->get('name'))
            ->setSurname($request->get('surname'))
            ->setPatronymic($request->get('patronymic'))
            ->setBirthdate($request->get('birthdate'))
            ->setPhone($request->get('phone_number'));

        $driverInfo->getCarInfo()
            ->setBrand($request->get('car_brand'))
            ->setModel($request->get('car_model'))
            ->setGovNumber($request->get('car_gov_number'))
            ->setColor($request->get('car_color'))
            ->setVin($request->get('car_vin'))
            ->setCreationYear($request->get('car_creation_year'))
            ->setRegSertificate($request->get('car_reg_sertificate'));

        $driverInfo->getDriverDocumentInfo()
            ->setSerialNumber($request->get('document_serial_number'))
            ->setUniqNumber($request->get('document_uniq_number'))
            ->setCounty($request->get('document_country'))
            ->setEndDate($request->get('document_end_date'))
            ->setIssueDate($request->get('document_issue_date'));

        $driverCreationResult = TaximeterConnector::createDriver($driverInfo);

        if (!$driverCreationResult['redirect'])
            return ResponseHandler::getJsonResponse(500, "Не удалось произвести регистрацию в таксометре");

        $code = (string)(rand(100000, 999999));

        $user = UserJWT::create([
            'name' => $request->get('name'),
            'patronymic' => $request->get('patronymic'),
            'surname' => $request->get('surname'),
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
