<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Roles",
 *     description="Opérations liées aux roles"
 * )
 */
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
     *     path="/api/roles",
     *     summary="Ajouter un rôle",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             example={
     *                 "nomRole": "string"
     *             }
     *         )
     *     ),
     *    security={{ "BearerAuth": {} }} ,
     *     tags={"Roles"}
     * )
     */
    public function store(Request $request)
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $validator = Validator::make(
            $request->all(),
            [
                'nomRole' => 'required|unique:roles'
            ]
        );
        $role = Role::create([
            "nomRole" => $request->nomRole
        ]);
        return response()->json([
            "message" => "Le role a été ajouté avec succès",
            "nomRole" => $role
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $role->delete();
        return response()->json([
            "message" => "Le role a été supprimé avec succès"
        ]);
    }
}
