<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations\OpenApi as OA;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

}
