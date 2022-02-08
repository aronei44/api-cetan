<?php
namespace App\Http\Controllers;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);
Route::middleware('auth:sanctum')->group(function(){
    Route::get('/image',[ImageController::class,'index']);
    Route::post('/image',[ImageController::class,'store']);
    Route::put('/image/{image}',[ImageController::class,'update']);
    Route::delete('/image/{image}',[ImageController::class,'destroy']);
    Route::post('/logout',[AuthController::class,'logout']);
});
