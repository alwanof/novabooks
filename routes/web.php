<?php

use App\Driver;
use App\Notifications\SendCredentials;
use App\Order;
use App\Role;
use App\Setting;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {



    return view('welcome');
});

Route::get('/taxi/{office_email}', 'ClientController@index')->name('client.create');
Route::post('/taxi/dist/order', 'ClientController@dist')->name('client.dist');
Route::post('/taxi/composse/order', 'ClientController@composse')->name('client.composse');
