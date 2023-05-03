<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Models\CodeQr;

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

Route::get('/', [\App\Http\Controllers\PostControllers::class, 'index']);

Route::get('/connexion', [\App\Http\Controllers\PostControllers::class, 'connexion']);
Route::post('/creer-connexion', [\App\Http\Controllers\AgentController::class, 'login']);

Route::get('/logout', [\App\Http\Controllers\PostControllers::class, 'logout']);

Route::get('/rapport-individuel', [\App\Http\Controllers\PostControllers::class, 'rapportIndividuel']);
Route::get('/rapport-individuel-agent', [\App\Http\Controllers\PostControllers::class, 'rapportIndividuelAgent']);

Route::get('/liste-agents', [\App\Http\Controllers\PostControllers::class, 'agents']);

Route::get('/ajouter-agent', [\App\Http\Controllers\AgentController::class, 'create']);
Route::post('/creer-agent', [\App\Http\Controllers\AgentController::class, 'store']);
Route::post('/menu/modifier-info-personnel', [\App\Http\Controllers\AgentController::class, 'update']);
Route::post('/menu/modifier-info-connexion', [\App\Http\Controllers\AgentController::class, 'update']);
Route::post('/profile_picture', [\App\Http\Controllers\AgentController::class, 'profile_pic']);

Route::get('/nouvelle-journee', [\App\Http\Controllers\PostControllers::class, 'nouvelleJournee']);

Route::get('/tableau-de-bord', [\App\Http\Controllers\PostControllers::class, 'tableauDeBord']);
Route::get('/historique-pointages', [\App\Http\Controllers\PostControllers::class, 'historiquePointages']);

Route::get('/menu', [\App\Http\Controllers\PostControllers::class, 'menu']);
Route::get('/menu/profil', [\App\Http\Controllers\PostControllers::class, 'profil']);
Route::get('/menu/modifier-profil', [\App\Http\Controllers\PostControllers::class, 'modifierProfil']);

Route::get('/delete-agent', [\App\Http\Controllers\AgentController::class, 'destroy']);

Route::get('/preload-data-form', [\App\Http\Controllers\PostControllers::class, 'preloadDataForm']);
Route::get('/update-qrcode', [\App\Http\Controllers\PostControllers::class, 'updateQrCode']);

Route::get('/scan', [\App\Http\Controllers\PostControllers::class, 'scanner']);
Route::post('/scanned', [\App\Http\Controllers\PostControllers::class, 'scan']);

Route::get('/preload-data-date', [\App\Http\Controllers\PostControllers::class, 'preloadDataDate']);
Route::get('/preload', [\App\Http\Controllers\PostControllers::class, 'preload']);

Route::get('/generer-rapport/{agent}/{date}', [\App\Http\Controllers\PostControllers::class, 'rapport']);
Route::get('/insert', [\App\Http\Controllers\PostControllers::class, 'insertPointage']);

Route::post('/login-form', [AuthController::class, 'login'])->name('login-form');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Route::get('/qrcode', function () {
//     $myuuid = "83700001376";
//     $path = '../public/qrcodes/2.svg';

//     return QrCode::size(200)->generate($myuuid, $path); 
// });