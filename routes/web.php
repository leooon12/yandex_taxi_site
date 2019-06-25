<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\AnotherClasses\Builders\DriverInfo;
use App\AnotherClasses\TaximeterConnector;
use Illuminate\Support\Facades\Mail;
use TCG\Voyager\Events\RoutingAdmin;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('/driver', 'DriverController', ['only' => [
    'index', 'store'
]]);

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    Route::group(['middleware' => 'admin.user'], function () {
        Route::get('withdrawal/', 'AdminPanelWithdrawalController@index');
        Route::get('edit_request/', 'AdminRequestController@index');
    });
});

if (App::environment('production', 'staging')) {
    URL::forceScheme('https');
}