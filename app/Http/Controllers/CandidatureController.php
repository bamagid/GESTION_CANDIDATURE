<?php

namespace App\Http\Controllers;

use App\Mail\DemandeDeCandidatureAccepter;
use App\Mail\DemandeDeCandidatureRefuser;
use App\Models\Candidature;
use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Candidatures",
 *     description="Opérations liées aux candidatures"
 * )
 */
class CandidatureController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/candidatures",
     *     summary="Lister toutes les candidatures",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *    security={{ "BearerAuth": {} }} ,
     *  tags={"Candidatures"}
     * )
     */
    public function index()
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }

        $candidatures = Candidature::all();
        return response()->json([
            "message" => "Voici la listes de toutes les candidatures",
            "candidatures" => $candidatures
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/users/candidatures",
     *     summary="Lister les candidatures de l'utilisateur connecté",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *    security={{ "BearerAuth": {} }} ,
     *     tags={"Candidatures"}
     * )
     */
    public function userCandidatures(Request $request)
    {
        if (auth()->user()->role_id == 1) {
            return response()->json(['message' => 'Non autorisé. Seuls les candidats peuvent voir leurs candidatures.'], 403);
        }

        foreach ($request->user()->candidatures as $candidature) {
            $candidatures[] = $candidature;
        }
        return response()->json([
            'message' => 'Voici vos candidatures',
            'candidatures' => $candidatures
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/acceptees",
     *     summary="Lister les candidatures acceptées",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *    security={{ "BearerAuth": {} }} ,
     *      tags={"Candidatures"}
     * )
     */
    public function accepter()
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $candidatures = Candidature::where('statut', 'accepte')->get();
        return response()->json([
            "message" => "Voici la listes de toutes les candidatures qui on été accepté",
            "candidatures" => $candidatures
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/en_attente",
     *     summary="Lister les candidatures en attente",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *      security={{ "BearerAuth": {} }} ,
     *      tags={"Candidatures"}
     * )
     */
    public function en_attente()
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $candidatures = Candidature::where('statut', 'en attente')->get();
        return response()->json([
            "message" => "Voici la listes de toutes les candidatures qui sont en attente",
            "candidatures" => $candidatures
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/refusees",
     *     summary="Lister les candidatures refusées",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *      security={{ "BearerAuth": {} }} ,
     *   tags={"Candidatures"}
     * )
     */
    public function refuser()
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $candidatures = Candidature::where('statut', 'refuse')->get();
        return response()->json([
            "message" => "Voici la listes de toutes les candidatures qui on été refusé",
            "candidatures" => $candidatures
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
     * @OA\Get(
     *     path="/api/formations/candidatures/{formation}",
     *     summary="Ajouter une candidature",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *     @OA\Parameter(in="path", name="formation",@OA\Schema(type="string")),
     *      security={{ "BearerAuth": {} }} ,
     *      tags={"Candidatures"}
     * )
     */
    public function store(Request $request, Formation $formation)
    {
        // Vérifier si l'utilisateur a le rôle "candidat"
        if (auth()->user()->role_id == 1) {
            return response()->json(['message' => 'Non autorisé. Seuls les candidats peuvent candidater.'], 403);
        }

        $candidature = Candidature::create([
            "user_id" => $request->user()->id,
            "formation_id" => $formation->id
        ]);
        return response()->json([
            "message" => "La candidature a bien été pris en compte on vous enverras un message si elle a eté accepté ou refuser pour vous en informer",
            "candidature" => $candidature
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/candidature/{candidature}",
     *     summary="Obtenir les détails d'une candidature",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *     @OA\Response(response="404", description="Candidature non trouvée", @OA\JsonContent(example="")),
     *     @OA\Parameter(in="path", name="candidature", required=true, @OA\Schema(type="integer")),
     *      security={{ "BearerAuth": {} }} ,
     *   tags={"Candidatures"}
     * )
     */
    public function show(Candidature $candidature)
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        return response()->json([
            "message" => "Voici la candidature que vous chercher",
            "candidature" => $candidature
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/candidatures/refuser/{candidature}",
     *     summary="Refuser une candidature",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *     @OA\Parameter(in="path", name="candidature", @OA\Schema(type="string")),
     *  security={
     *         {"BearerAuth": {}}
     *     },
     *  tags={"Candidatures"}
     * )
     */
    public function edit(Candidature $candidature)
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $candidature->update([
            "statut" => "refuse"
        ]);
        Mail::to($candidature->user->email)->send(new DemandeDeCandidatureRefuser());
        return response()->json([
            "message" => "Le refus de la candidature a reussi",
            "candidature" => $candidature
        ]);
    }
    /**
     * @OA\Put(
     *     path="/api/candidatures/accepter/{candidature}",
     *     summary="Accepter une candidature",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *     @OA\Parameter(in="path", name="candidature", @OA\Schema(type="string")),
     *      security={{ "BearerAuth": {} }} ,
     *      tags={"Candidatures"}
     * )
     */

    public function update(Request $request, Candidature $candidature)
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $candidature->update([
            "statut" => "accepte"
        ]);
        Mail::to($candidature->user->email)->send(new DemandeDeCandidatureAccepter());

        return response()->json([
            "message" => "La candidature a bien été accepté",
            "candidature" => $candidature
        ]);
    }


    public function destroy(Candidature $candidature)
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $candidature->delete();
        return response()->json([
            "message" => "Cette candidature a été bien supprimer",
        ]);
    }
}
