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

// Route pour afficher la liste des formations
Route::get('/formations', [FormationController::class, 'index']);
// Route pour afficher les détails d'une formation spécifique
Route::get('/formations/{formation}', [FormationController::class, 'show']);
//connecter un utilisateur 
Route::post("/login", [UserController::class, 'login']);
//Ajouter enregistrer un candidat
Route::post("/users", [UserController::class, 'store']);
Route::middleware('auth:api')->group(function () {
    /*Route pour la gestion des candidatures */
    // Route pour enregistrer une nouvelle candidature
    Route::get('/formations/candidatures/{formation}', [CandidatureController::class, 'store']);
    // Route pour voir les candidatures d'un apprenant
    Route::get('/users/candidatures', [CandidatureController::class, 'userCandidatures']);
    // Route pour afficher la liste de toutes les candidatures
    Route::get('/candidatures', [CandidatureController::class, 'index']);
    // Route pour afficher les détails d'une candidature spécifique
    Route::get('/candidature/{candidature}', [CandidatureController::class, 'show']);
    // Route pour afficher la liste des candidatures en attente
    Route::get('en_attente', [CandidatureController::class, 'en_attente']);
    // Route pour afficher la liste des candidatures acceptées
    Route::get('acceptees', [CandidatureController::class, 'accepter']);
    // Route pour afficher la liste des candidatures refusées
    Route::get('refusees', [CandidatureController::class, 'refuser']);
    // Route pour refuser une candidature
    Route::put('/candidatures/refuser/{candidature}', [CandidatureController::class, 'edit']);
    // Route pour accepter une candidature
    Route::put('/candidatures/accepter/{candidature}', [CandidatureController::class, 'update']);
    /*Route pour la gestion des formations */
    // Route pour afficher les candidatures acceptées pour une formation
    Route::get('/formations/candidature_accepter/{formation}', [FormationController::class, 'candidature_accepter']);
    // Route pour afficher les candidatures en attente pour une formation
    Route::get('/formations/candidature_en_attente/{formation}', [FormationController::class, 'candidature_en_attente']);
    // Route pour afficher les candidatures refusées pour une formation
    Route::get('/formations/candidature_refuser/{formation}', [FormationController::class, 'candidature_refuser']);
    Route::resource('formation', FormationController::class);
    Route::post('formation/{formation}', [FormationController::class, 'update']);
    Route::get('archive_formation/{formation}', [FormationController::class, 'archive']);
    Route::get('cloture_formation/{formation}', [FormationController::class, 'cloturer']);
    /*Route pour la gestion des roles */
    Route::resource('roles', RoleController::class);
    /*Route pour la gestion des utilisateurs */
    Route::resource('user', UserController::class);
    Route::resource('admin', UserController::class);
    //afficher la liste de tous les candidats
    Route::get("candidats",[UserController::class,"index"]);
    //afficher les candidats pour une formations donnée
    Route::get("candidats/{formation}",[FormationController::class,"candidats_formation"]);
    //deconnecter un utilisateur 
    Route::get("/logout", [UserController::class, 'logout']);
    //regeneration du token jwt
    Route::post("/refresh_token", [UserController::class, 'refreshToken']);
});
