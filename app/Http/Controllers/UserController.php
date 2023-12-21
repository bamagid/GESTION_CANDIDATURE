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
     *     path="/users",
     *     tags={"Utilisateurs"},
     *     summary="Récupère la liste des utilisateurs",
     *     description="Renvoie une liste de tous les utilisateurs",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des utilisateurs récupérée avec succès"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non autorisé"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Interdit"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Non trouvé"
     *     )
     * )
     */
    public function index()
    {
        $candidats=User::where('role_id',2)->get();
        return response()->json([
            "message"=>"La liste de tous les candidats",
            'candidats'=>$candidats
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // data validation
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

        // User Model
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


    /**
     * Display the specified resource.
     */
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Validation
        // data validation
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
            "candidat"=>$user
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    // User Login (POST, formdata)
    public function login(Request $request)
    {

        // data validation
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

    // Regenerer le token jwt 
    public function refreshToken()
    {

        $newToken = auth()->refresh();

        return response()->json([
            "status" => true,
            "message" => "Voici votre nouveau token",
            "token" => $newToken
        ]);
    }

    //Deconnexion de l'utilisateur
    public function logout()
    {

        auth()->logout();

        return response()->json([
            "status" => true,
            "message" => "Utilisateur deconnecté avec succes"
        ]);
    }
}
