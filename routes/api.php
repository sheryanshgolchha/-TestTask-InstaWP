<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GeneralController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['jwt.verify']], function() {

    Route::put('add_wallet', [GeneralController::class, 'add_wallet']);
    Route::put('buy_a_cookie', [GeneralController::class, 'buy_a_cookie']);

    Route::get('logout', [AuthController::class, 'logout']);
});

Route::any('{path}', function() {
    $response['status'] = 'error';
    $response['response'] = "API not found";
    return response()->json($response, 404);
})->where('path', '.*');
