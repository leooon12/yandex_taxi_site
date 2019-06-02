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


//Регистрация пользователя
Route::post('/register', 'UserController@register');
Route::post('/login', 'UserController@login');
Route::post('/recovery', 'UserController@recovery');

Route::get('/taximetr', 'UserController@taximetr');

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('user', 'UserController@getAuthenticatedUser');
    Route::get('driver', 'DriverController@getDriver');

    //Редактирование данных
    Route::post('user/edit', 'UserController@edit');

    Route::group(['prefix' => 'withdrawal'], function() {
        Route::post('bankcard', 'WithdrawalController@withdrawalBankCard');
        Route::post('yandex', 'WithdrawalController@withdrawalYandex');
        Route::post('bank_account', 'WithdrawalController@withdrawalBankAccount');
        Route::get('last', 'WithdrawalController@getLastWithdrawal');
    });

});

Route::resource('/edit_request', 'EditRequestController', ['only' => [
    'index', 'store'
]]);

Route::get('withdrawal/{type?}','AdminPanelWithdrawalController@get_withdrawals');

Route::group(['prefix' => 'info'], function() {
    Route::get('cars/{brandName}', 'CarModelsController@show');
});