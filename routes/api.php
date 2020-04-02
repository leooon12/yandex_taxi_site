<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('/driver', 'DriverController', ['only' => [
    'index', 'store'
]]);


Route::namespace('JWTAuth')->group(function () {
    Route::post('/register',    'RegisterController@register');

    Route::group(['middleware' => ['jwt.verify']], function() {
        Route::post('/taximeter_register', 'RegisterController@taximeterRegister');
    });

    Route::post('/login',       'LoginController@login');
});

Route::get('/taximetr', 'UserController@taximetr');



/*Route::group(['prefix' => 'test'], function() {
    Route::get('get_driver_profiles/{filter_string}', 'FleetController@getDriverProfiles');
    Route::get('get_driver_profiles_without_filter', 'FleetController@getDriverProfilesWithoutFilter');
    Route::get('create_transaction/{amount}/{driver_id}', 'FleetController@createTransaction');
});*/

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('user', 'UserController@getAuthenticatedUser');
    Route::get('driver', 'DriverController@getDriver');

    //Редактирование данных
    Route::post('driver/phone/edit', 'DriverController@changeNumber');
    Route::post('driver/car/edit', 'DriverController@changeCar');
    Route::post('driver/car/existing/edit', 'DriverController@changeExistingCar');

    //Получение всех авто пользователя
    Route::get('driver/cars', 'DriverController@getUserCars');

    Route::group(['prefix' => 'withdrawal'], function() {
        Route::post('bankcard', 'WithdrawalController@withdrawalBankCard');
        Route::post('yandex', 'WithdrawalController@withdrawalYandex');
        Route::post('bank_account', 'WithdrawalController@withdrawalBankAccount');
        Route::get('last', 'WithdrawalController@getLastWithdrawal');
    });

    //Получение всех универсальных заявок пользователя
    Route::get('user/requests', 'AdminRequestController@get_user_requests');

});

Route::get('withdrawal_statuses','AdminPanelWithdrawalController@get_all_statuses');

Route::group(['prefix' => 'withdrawal'], function() {
    Route::get('/{type?}','AdminPanelWithdrawalController@get_withdrawals');
    Route::post('/status','AdminPanelWithdrawalController@change_status');
});

Route::group(['prefix' => 'edit_request'], function() {
    Route::get('/{type?}','AdminRequestController@get_requests');
    Route::post('/','AdminRequestController@store');
    Route::post('/status','AdminRequestController@change_status');
});


Route::group(['prefix' => 'info'], function() {
    Route::get('cars/{brandName}', 'CarModelsController@show');
});
