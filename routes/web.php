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
        Route::get('withdrawal/', ['uses' => 'AdminPanelWithdrawalController@index',  'as' => 'voyager.withdrawal.index']);
        Route::get('topUp/{type}/{id}', ['uses' => 'AdminPanelWithdrawalController@show',  'as' => 'voyager.withdrawal.show']);
        Route::get('topUp', ['uses' => 'AdminPanelWithdrawalController@get_top_up_withdrawal', 'as' => 'voyager.topUpWithdrawal.index']);
        Route::get('edit_request/', ['uses' => 'AdminRequestController@index', 'as' => 'voyager.editRequests.index']);

        //Нужен мидлварь от админа, а из api он не работает
        Route::group(['prefix' => 'withdrawal/topUp/'], function() {
            Route::post('bankCard', 'AdminPanelWithdrawalController@top_up_withdrawal');
            Route::post('qiwi',     function () {
                return "new topUp function here";
            });
        });
    });

});

if (App::environment('production', 'staging')) {
    URL::forceScheme('https');
}
