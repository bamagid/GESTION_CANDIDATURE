<?php

use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Route pour afficher la liste de toutes les candidatures
Route::get('/candidatures', [CandidatureController::class, 'index']);
// Route pour afficher les détails d'une candidature spécifique
Route::get('/candidatures/{candidature}', [CandidatureController::class, 'show']);
// Route pour afficher la liste des formations
Route::get('/formations', [FormationController::class, 'index']);
// Route pour afficher les détails d'une formation spécifique
Route::get('/formations/{formation}', [FormationController::class, 'show']);
//connecter un utilisateur 
Route::post("/login", [UserController::class, 'login']);
Route::middleware('auth:api')->group(function(){
/*Route pour la gestion des candidatures */
// Route pour enregistrer une nouvelle candidature
Route::post('/formations/{formation}/candidatures', [CandidatureController::class, 'store']);
// Route pour voir les candidatures d'un apprenant
Route::get('/users/candidatures', [CandidatureController::class, 'userCandidatures']);
// Route pour afficher la liste des candidatures acceptées
Route::get('/candidatures/acceptees', [CandidatureController::class, 'accepter']);
// Route pour afficher la liste des candidatures en attente
Route::get('/candidatures/en_attente', [CandidatureController::class, 'en_attente']);
// Route pour afficher la liste des candidatures refusées
Route::get('/candidatures/refusees', [CandidatureController::class, 'refuser']);
// Route pour refuser une candidature
Route::put('/candidatures/{candidature}/refuser', [CandidatureController::class, 'edit']);
// Route pour accepter une candidature
Route::put('/candidatures/{candidature}/accepter', [CandidatureController::class, 'update']);
/*Route pour la gestion des formations */
// Route pour afficher les candidatures acceptées pour une formation
Route::get('/formations/candidature_accepter/{formation}', [FormationController::class, 'candidature_accepter']);
// Route pour afficher les candidatures en attente pour une formation
Route::get('/formations/candidature_en_attente/{formation}', [FormationController::class, 'candidature_en_attente']);
// Route pour afficher les candidatures refusées pour une formation
Route::get('/formations/candidature_refuser/{formation}', [FormationController::class, 'candidature_refuser']);
Route::resource('formation', FormationController::class);
Route::post('formation/{formation}', [FormationController::class, 'update']);
Route::post('archive_formation/{formation}', [FormationController::class, 'archive']);
Route::post('cloture_formation/{formation}', [FormationController::class, 'cloturer']);
/*Route pour la gestion des roles */
Route::resource('roles', RoleController::class);
/*Route pour la gestion des utilisateurs */
Route::resource('user', UserController::class);
Route::resource('admin', UserController::class);
//deconnecter un utilisateur 
Route::post("/logout", [UserController::class, 'logout']);
//regeneration du token jwt
Route::post("/refresh_token", [UserController::class, 'refreshToken']);
});
