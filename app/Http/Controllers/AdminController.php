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
     * @OA\Post(
     *     path="/api/admin",
     *     summary="Inscrire un admin",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *     @OA\Parameter(in="header", name="User-Agent", @OA\Schema(type="string")),
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
     *      tags={"Utilisateurs"}
     * )
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
