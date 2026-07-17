<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post("login", "Auth\LoginController@loginAPI");

Route::get('healthz', 'Api\BbbbController@health');

Route::prefix('v1')->group(function () {
    Route::post('auth/login', 'Api\BbbbController@login');
    Route::get('bible/versions', 'Api\BbbbController@listBibleVersions');
    Route::get('bible/versions/{versionCode}', 'Api\BbbbController@getBibleVersion');
    Route::get('bible/versions/{versionCode}/download', 'Api\BbbbController@getBibleVersion');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', 'Api\BbbbController@me');
        Route::get('reading-logs/today', 'Api\BbbbController@getTodayReadingLogs');
        Route::put('reading-logs/today', 'Api\BbbbController@replaceTodayReadingLogs');
        Route::get('posts', 'Api\BbbbController@listPosts');
        Route::post('posts', 'Api\BbbbController@createPost');
        Route::post('posts/{postId}/publish', 'Api\BbbbController@publishPost');
        Route::get('posts/{postId}/comments', 'Api\BbbbController@listComments');
        Route::post('posts/{postId}/comments', 'Api\BbbbController@createComment');
        Route::put('posts/{postId}/reactions/{kind}', 'Api\BbbbController@setReaction');
        Route::get('feed', 'Api\BbbbController@feed');
    });
});
