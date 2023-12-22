<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations\OpenApi as OA;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Utilisateurs",
 *     description="Opérations liées aux utilisateurs"
 * )
 */

class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Afficher la liste de tous les candidats de la plateforme", 
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *    security={{ "BearerAuth": {} }} ,
     *  tags={"Utilisateurs"}
     * )
     */

    public function index()
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $candidats = User::where('role_id', 2)->get();
        return response()->json([
            "message" => "La liste de tous les candidats",
            'candidats' => $candidats
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    /**
     * @OA\Post(
     *     path="/api/users",
     *     summary="Enregistrer un candidat ", 
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             example={
     *                 "nom": "string",
     *                 "prenom": "string",
     *                 "adresse": "string",
     *                 "telephone": "string",
     *                 "email": "string",
     *                 "password": "string"
     *             }
     *         )
     *     ),
     *    security={{ "BearerAuth": {} }} ,
     *  tags={"Utilisateurs"}
     * )
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "nom" => "required|alpha",
            "prenom" => "required|regex:/^[a-zA-z ]+$/",
            "adresse" => ['required', 'string', 'regex:/^[a-zA-Z0-9 ]+$/'],
            'telephone' => "required|numeric",
            "email" => "required|email|unique:users",
            "password" => Rules\Password::defaults()
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        User::create([
            "nom" => $request->nom,
            "prenom" => $request->prenom,
            "adresse" => $request->adresse,
            "telephone" => $request->telephone,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "role_id" => 2
        ]);

        // Response
        return response()->json([
            "status" => true,
            "message" => "Utilisateur enregistrer avec succés "
        ]);
    }



    public function show(User $user)
    {
        $userdata = auth()->user();

        return response()->json([
            "status" => true,
            "message" => "L'utilisateur connecté",
            "data" => $userdata
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
    }

    /**
     * @OA\Put(
     *     path="/api/user/{user}",
     *     summary="Modifier les infos d'un candidat",
     *   
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *
     *     @OA\Parameter(in="path", name="user", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             example={
     *                 "nom": "string",
     *                 "prenom": "string",
     *                 "adresse": "string",
     *                 "telephone": "string",
     *                 "email": "string",
     *                 "password": "string"
     *             }
     *         )
     *     ),
     *    security={{ "BearerAuth": {} }} ,
     *  tags={"Utilisateurs"}
     * )
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            "nom" => "required|alpha",
            "prenom" => "required|regex:/^[a-zA-z ]+$/",
            "adresse" => ['required', 'string', 'regex:/^[a-zA-Z0-9 ]+$/'],
            'telephone' => "required|numeric",
            "email" => "sometimes|email|unique:users,email",
            "password" => Rules\Password::defaults()
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Modification des données de l'utilisateur
        $user->update([
            "nom" => $request->nom,
            "prenom" => $request->prenom,
            "adresse" => $request->adresse,
            "telephone" => $request->telephone,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "role_id" => 2
        ]);
        return response()->json([
            "status" => true,
            "message" => "Informations modifiées avec succès !",
            "candidat" => $user
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }


    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Connecter un utilisateur",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             example={
     *                 "email": "string",
     *                 "password": "string"
     *             }
     *         )
     *     ),
     *    security={{ "BearerAuth": {} }} ,
     * tags={"Utilisateurs"}
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        // Authentification avec du jwt
        $token = JWTAuth::attempt([
            "email" => $request->email,
            "password" => $request->password
        ]);

        if (!empty($token)) {

            return response()->json([
                "status" => true,
                "message" => "L'utilisateur est connecté avec succés",
                "token" => $token
            ]);
        }

        return response()->json([
            "status" => false,
            "message" => "Ces infos ne correspondent a aucun de nos utilisateurs"
        ], 404);
    }
    /**
     * @OA\Post(
     *     path="/api/refresh_token",
     *     summary="Regénérer le token",  
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *    security={{ "BearerAuth": {} }},
     *     tags={"Utilisateurs"}
     * )
     */
    public function refreshToken()
    {

        $newToken = auth()->refresh();

        return response()->json([
            "status" => true,
            "message" => "Voici votre nouveau token",
            "token" => $newToken
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/logout",
     *     summary="Se déconnecter",
     *   
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *    security={{ "BearerAuth": {} }} ,
     * tags={"Utilisateurs"}
     * )
     */
    public function logout()
    {

        auth()->logout();

        return response()->json([
            "status" => true,
            "message" => "Utilisateur deconnecté avec succes"
        ]);
    }
}
