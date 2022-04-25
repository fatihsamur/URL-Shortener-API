<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ShortLinkController;
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

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('me', [AuthController::class, 'me'])->middleware('auth:sanctum')->name('me');
Route::post('logout', [AuthController::class, 'logOut'])->middleware('auth:sanctum')->name('logout');

Route::post('generate-short-url', [ShortLinkController::class, 'store'])->middleware('auth:sanctum')->name('short-url.store');
Route::get('short-url/{code}', [ShortLinkController::class, 'shortenedLink'])->name('short-url.shortenedLink');

// get all short links of a user
Route::get('user-short-Links', [ShortLinkController::class, 'getAllShortLinks'])->middleware('auth:sanctum')->name('short-url.getAllShortLinks');
// delete shortlink by id
Route::delete('user-short-Link/{id}', [ShortLinkController::class, 'deleteShortLink'])->middleware('auth:sanctum')->name('short-url.deleteShortLink');
