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
		return redirect('dashboard');
	} else {
		return view('welcome');
	}
});

Auth::routes();

Route::get('/testemail', function() {
	$message = "Hello World!!!!";
    Mail::send('welcome', ['title' => 'Test Email', 'message' => $message], function($message) {
        $message->to('donny.p.perdana@gmail.com')->subject('Testing mails');
    });
});

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/profile', 'HomeController@profile')->name('profile');
// Route::get('/book', 'HomeController@book')->name('book');

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

// System Configuration > Inventory Status
Route::resource('inventorystatuses', 'InventoryStatusController')->only('index', 'create', 'store', 'edit');
Route::post('inventorystatus/{inventorystatus}/update', 'InventoryStatusController@update')->name('inventorystatuses.update');
Route::post('inventorystatus/{inventorystatus}/delete', 'InventoryStatusController@delete')->name('inventorystatuses.delete');

// System Configuration > Allocation
Route::resource('allocations', 'AllocationController')->only('index', 'create', 'store', 'edit');
Route::post('allocation/{allocation}/update', 'AllocationController@update')->name('allocations.update');
Route::post('allocation/{allocation}/delete', 'AllocationController@delete')->name('allocations.delete');

// System Configuration > Sales Status
Route::resource('salesstatuses', 'SalesStatusController')->only('index', 'create', 'store', 'edit');
Route::post('salesstatus/{salesstatus}/update', 'SalesStatusController@update')->name('salesstatuses.update');
Route::post('salesstatus/{salesstatus}/delete', 'SalesStatusController@delete')->name('salesstatuses.delete');

// System Configuration > Buyback Status
Route::resource('buybackstatuses', 'BuybackStatusController')->only('index', 'create', 'store', 'edit');
Route::post('buybackstatus/{buybackstatus}/update', 'BuybackStatusController@update')->name('buybackstatuses.update');
Route::post('buybackstatus/{buybackstatus}/delete', 'BuybackStatusController@delete')->name('buybackstatuses.delete');

// System Configuration > Users
Route::resource('users', 'UserController')->only('index', 'create', 'store', 'edit');
Route::get('users/{user}/changepassword', 'UserController@changepassword')->name('users.changepassword');
Route::post('users/{user}/update', 'UserController@update')->name('users.update');
Route::post('users/{user}/delete', 'UserController@delete')->name('users.delete');
Route::post('users/{user}/updatepassword', 'UserController@updatepassword')->name('users.updatepassword');

// System Configuration > Roles
Route::resource('roles', 'RoleController')->only('index', 'create', 'store', 'edit');
Route::post('roles/{role}/update', 'RoleController@update')->name('roles.update');
Route::post('roles/{role}/delete', 'RoleController@delete')->name('roles.delete');

// Book (Items)
Route::resource('items', 'ItemController')->only('index', 'create', 'store', 'edit');
Route::post('item/{item}/update', 'ItemController@update')->name('items.update');
Route::post('item/{item}/delete', 'ItemController@delete')->name('items.delete');
Route::get('items/bulkupload', 'ItemController@bulkupload')->name('items.bulkupload');
Route::get('items/downloadcsvtemplate', 'ItemController@downloadCsvTemplate')->name('items.downloadcsvtemplate');
Route::post('items/importcsv', 'ItemController@importcsv')->name('items.importcsv');
Route::post('items/massaction', 'ItemController@massaction')->name('items.massaction');

Route::get('employee/items/index', 'ItemController@employeeItemIndex')->name('items.employee.index');
Route::get('employee/items/create', 'ItemController@employeeItemCreate')->name('items.employee.create');
Route::post('employee/items/store', 'ItemController@employeeItemStore')->name('items.employee.store');
Route::post('employee/items/find', 'ItemController@employeeItemFind')->name('items.employee.find');
Route::get('employee/sales/entry', 'ItemController@employeeSalesEntry')->name('sales.employee.entry');
Route::get('employee/sales/form/{itemId}', 'ItemController@employeeSalesForm')->name('sales.employee.form');
Route::post('employee/sales/form/save', 'ItemController@employeeSalesFormSave')->name('sales.employee.form.save');

// Dashboard
Route::resource('dashboard', 'DashboardController')->only('index');

// Sales for Employee

Route::post('/profile/update', 'UserController@profileUpdate')->name('profile.update');
