<?php
/**
 * Created by PhpStorm.
 * User: fireeagle
 * Date: 12.06.19
 * Time: 22:58
 */

namespace App\Http\Controllers\JWTAuth;


use App\AnotherClasses\Builders\DriverInfo;
use App\AnotherClasses\ResponseHandler;
use App\AnotherClasses\TaximeterConnector;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserTaximeterRegisterRequest;
use App\Jobs\SendRegistrationSms;
use App\UserJWT;
use JWTAuth;

class RegisterController extends Controller
{

    //Допилить
    public function register(UserRegisterRequest $request) {
        $code = (string)(rand(100000, 999999));

        UserJWT::updateOrCreate(
            ['phone_number' => $request->get('phone_number')],
            ['password' => bcrypt($code)]
        );

        $regSms = new SendRegistrationSms($request->get('phone_number'), $code);
        $this->dispatch($regSms);

        return ResponseHandler::getJsonResponse(200, "Вы успешно зарегистрированы, ожидайте смс-пароля");

    }

    public function taximeterRegister(UserTaximeterRegisterRequest $request) {

        $user_id = JWTAuth::parseToken()->authenticate()->id;

        $user = UserJWT::where('id', $user_id)->first();

        $driverInfo = new DriverInfo();

        $driverInfo->setName($request->get('name'))
            ->setSurname($request->get('surname'))
            ->setPatronymic($request->get('patronymic'))
            ->setBirthdate($request->get('birthdate'))
            ->setPhone($user->phone_number);

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


        $user->name = $request->get('name');
        $user->patronymic = $request->get('patronymic');
        $user->surname = $request->get('surname');

        $user->save();

        return ResponseHandler::getJsonResponse(200, "Вы успешно зарегистрированы", compact('user'));
    }
}