<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\ApiController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\Agent;
use App\Models\User;
use App\Models\Pointage;
use App\Models\Service;
use App\Models\Sous_direction;
use App\Models\CodeQr;

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

Route::middleware('web')->group(function () {
    Route::post('/login', [AgentController::class, 'login']);
    Route::post('/check-token-validity', [ApiController::class, 'checkTokenValidity']);
});

Route::group(["middleware" => ["auth:sanctum"]], function () {
    Route::get('/agents', [AgentController::class, 'index']);
    Route::get('/agents/{id}', [AgentController::class, 'show']);
    Route::delete('/delete/agents/{id}', [AgentController::class, 'destroy']);
    Route::post('/agents/register', [AgentController::class, 'store']);
    Route::put('/agents/update/{id}', [AgentController::class, 'update']);

    Route::post('/scan', [ApiController::class, 'scan']);

    Route::get('/status', [ApiController::class, 'currentStatus']);

    Route::get('/new-day', [ApiController::class, 'newDay']);
    Route::get('/check-new-day', [ApiController::class, 'checkNewDay']);

    Route::get('/sous-direction', [ApiController::class, 'sous_direction']);
    Route::get('/fonction', [ApiController::class, 'fonction']);
    Route::get('/service', [ApiController::class, 'service']);
    Route::get('/direction', [ApiController::class, 'direction']);

    Route::get('/token/{id}', [ApiController::class, 'token']);
    Route::get('/profil/{id}', [ApiController::class, 'profils']);
});

Route::get('/check-token-validity', function () {
    $authenticated = auth()->check();
    return response()->json([
        'authenticated' => ($authenticated) ? true : false,
    ]);
})->middleware('auth:sanctum');

Route::delete('/logout', function () {
    if (auth()->check()) {
        auth()->user()->tokens->each(function($token, $key){
            $token->delete();
        });
        return response()->json([
            "success" => true,
            'message' => 'Deconnexion',
            'status' => 200
        ], 200);
    }
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', [ApiController::class, 'profilUser']);
