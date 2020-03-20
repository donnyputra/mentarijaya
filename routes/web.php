<?php

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
	if(Auth::check()) {
		return redirect('home');
	} else {
		return view('welcome');
	}
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/profile', 'HomeController@profile')->name('profile');
Route::get('/book', 'HomeController@book')->name('book');

Route::get('/preferences/store', 'PreferencesController@store')->name('preferences.store');
Route::get('/preferences/category', 'PreferencesController@category')->name('preferences.category');
Route::get('/preferences/allocation', 'PreferencesController@allocation')->name('preferences.allocation');
Route::get('/preferences/itemstatus', 'PreferencesController@itemStatus')->name('preferences.itemstatus');
Route::get('/preferences/buybackstatus', 'PreferencesController@buybackStatus')->name('preferences.buybackstatus');
Route::get('/preferences/salesstatus', 'PreferencesController@salesStatus')->name('preferences.salesstatus');

Route::post('/profile/update', 'UserController@profileUpdate')->name('profile.update');
