<?php

namespace App\Http\Controllers;

use App\AnotherClasses\Builders\CarInfo;
use App\AnotherClasses\Builders\FullDriverInfo;
use App\AnotherClasses\TaximeterConnector;
use App\Http\Requests\ChangeDriverCarRequest;
use App\Http\Requests\ChangeDriverPhoneRequest;
use App\Http\Requests\DriverDataRequest;
use App\Jobs\SendRegistrationSms;
use App\Mail\DriverRequestMail;
use Illuminate\Support\Facades\Mail;
use App\AnotherClasses\ResponseHandler;
use JWTAuth;
use App\UserJWT;

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

        if ($taximeter_user_data)
            return ResponseHandler::getJsonResponse(200, "данные успешно получены", compact('taximeter_user_data'));

        return ResponseHandler::getJsonResponse(404, "Пользователь с таким номером не зарегистрирован в таксометре");
    }

    protected function getFullDriverInfo($user_phone_number) {
        $profile = TaximeterConnector::getDriverProfile($user_phone_number);
        
        if (!$profile)
            return null;

        $versionImeiPassword = TaximeterConnector::getAdditionalDriverInfo($user_phone_number);

        $driverInfo = new FullDriverInfo();
        $driverInfo->setAllFromTaximeterDriverProfile($profile);
        $driverInfo->setImei($versionImeiPassword[1]);
        $driverInfo->setTaximeterVesion($versionImeiPassword[0]);
        $driverInfo->setPassword($versionImeiPassword[2]);

        return $driverInfo;
    }

    public function changeNumber(ChangeDriverPhoneRequest $request) {
        $user_id = JWTAuth::parseToken()->authenticate()->id;
        $user_phone_number = UserJWT::where('id', $user_id)->first()->phone_number;

        $driverInfo = $this->getFullDriverInfo($user_phone_number);

        if (!$driverInfo)
            return ResponseHandler::getJsonResponse(404, "Пользователь с таким номером не зарегистрирован в таксометре");

        $driverInfo->setPhone($request->get('phone_number'));

        $editResponce = TaximeterConnector::editDriver($driverInfo);

        if (!isset($editResponce['reload']))
            return ResponseHandler::getJsonResponse(500, "Номер телефона не изменен", compact('editResponce'));

        $user = UserJWT::where('id', $user_id)
            ->update([
                'phone_number' => $request->get('phone_number')
            ]);

        $code = (string)(rand(100000, 999999));

        UserJWT::where('id', $user_id)
            ->update(['password' => bcrypt($code)]);

        JWTAuth::invalidate(JWTAuth::getToken());

        return ResponseHandler::getJsonResponse(228, "Номер телефона изменен", compact('user', 'token'));
    }

    public function changeCar(ChangeDriverCarRequest $request) {
        $user_id = JWTAuth::parseToken()->authenticate()->id;
        $user_phone_number = UserJWT::where('id', $user_id)->first()->phone_number;

        $carInfo = (new CarInfo())
            ->setBrand($request->get('car_brand'))
            ->setModel($request->get('car_model'))
            ->setGovNumber($request->get('car_gov_number'))
            ->setColor($request->get('car_color'))
            ->setVin($request->get('car_vin'))
            ->setCreationYear($request->get('car_creation_year'))
            ->setRegSertificate($request->get('car_reg_sertificate'));

/*        $carCreationResult = TaximeterConnector::createCar($carInfo);

        if (!isset($carCreationResult['id']))
            return ResponseHandler::getJsonResponse(500, "Не удалось добавить автомобиль в таксометр", compact('carCreationResult'));*/
        
        // Машина сохранена
        $driverInfo = TaximeterConnector::getDriverProfile($user_phone_number);

        TaximeterConnector::changeCar($driverInfo["driver"]["id"], "62b52c99edae641cff6a8fd37b67a1ed");

        if (!$driverInfo)
            return ResponseHandler::getJsonResponse(404, "Пользователь с таким номером не зарегистрирован в таксометре");

        
        return ResponseHandler::getJsonResponse(228, "Данные автомобиля изменены");
    }
}
