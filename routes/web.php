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

// System Configuration > Stores
Route::resource('stores', 'StoreController')->only('index', 'create', 'store', 'edit');
Route::post('stores/{store}/update', 'StoreController@update')->name('stores.update');
Route::post('stores/{store}/delete', 'StoreController@delete')->name('stores.delete');

// System Configuration > Categories
Route::resource('categories', 'CategoryController')->only('index', 'create', 'store', 'edit');
Route::post('category/{category}/update', 'CategoryController@update')->name('categories.update');
Route::post('category/{category}/delete', 'CategoryController@delete')->name('categories.delete');

// System Configuration > Item Status
Route::resource('itemstatuses', 'ItemStatusController')->only('index', 'create', 'store', 'edit');
Route::post('itemstatus/{itemstatus}/update', 'ItemStatusController@update')->name('itemstatuses.update');
Route::post('itemstatus/{itemstatus}/delete', 'ItemStatusController@delete')->name('itemstatuses.delete');

Route::post('/profile/update', 'UserController@profileUpdate')->name('profile.update');
