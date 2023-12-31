<?php

namespace App\Http\Controllers;

use App\Models\Candidature;
use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Formations",
 *     description="Opérations liées aux Formations"
 * )
 */
class FormationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/formations",
     *     summary="Lister toutes les formations",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *     tags={"Formations"}
     * )
     */
    public function index()
    {
        $formations = Formation::where('is_deleted', false)->get();
        return response()->json([
            'status' => true,
            "message" => "Voici la liste des formations disponible",
            'formations' => $formations,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/formations/candidature_accepter/{formation}",
     *     summary="Voir les candidatures acceptées pour une formation",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *   @OA\Parameter(in="path", name="formation", @OA\Schema(type="string")),
     *  security={
     *         {"BearerAuth": {}}
     *     },
     *      tags={"Formations"}
     * )
     */
    public function candidature_accepter(Formation $formation)
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        foreach ($formation->candidatures as $candidature) {
            if ($candidature->statut === "accepte") {

                $candidatures[] = $candidature;
            }
        }
        return response()->json([
            "message" => "Les candidatures de cette formations qui ont été accepté sont les suivants ",
            "candidature" => $candidatures
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/formations/candidature_en_attente/",
     *     summary="Voir les candidatures  en attente pour une formation",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *  security={
     *         {"BearerAuth": {}}
     *     },
     *        tags={"Formations"}
     * )
     */
    public function candidature_en_attente(Formation $formation)
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        foreach ($formation->candidatures as $candidature) {
            if ($candidature->statut === "en attente") {
                $candidatures[] = $candidature;
            }
        }

        return response()->json([
            "message" => "Les candidatures de cette formations qui sont en attente sont les suivants ",
            "candidature" => $candidatures
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/formation",
     *     summary="Ajouter une formation",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             example={
     *                 "libelle": "string",
     *                 "description": "string",
     *                 "dateCloture": "string",
     *                 "dateDebut": "string",
     *                 "duree": "string",
     *                 "image": "string"
     *             }
     *         )
     *     ),
     *  security={
     *         {"BearerAuth": {}}
     *     },
     *   tags={"Formations"}
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
                'libelle' => ['required', 'string'],
                'description' => ['required', 'string'],
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'dateCloture' => 'required|date|after:now',
                'dateDebut' => 'required|date|after:dateCloture',
                'duree' => 'required|string',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }
        $formation = Formation::create(
            [
                'libelle' => $request->libelle,
                'description' => $request->description,
                'image' => $imagePath,
                'dateCloture' => $request->dateCloture,
                'dateDebut' => $request->dateDebut,
                'duree' => $request->duree,
                'user_id' => auth()->user()->id
            ]
        );
        return response()->json([
            'message' => 'La formation a bien été ajoutée',
            'data' => $formation
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/formation/{formation}",
     *     summary="Obtenir les détails d'une formation",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *     @OA\Response(response="404", description="Formation non trouvée", @OA\JsonContent(example="")),
     *   @OA\Parameter(in="path", name="formation", @OA\Schema(type="string")),
     *  security={
     *         {"BearerAuth": {}}
     *     },
     *      tags={"Formations"}
     * )
     */
    public function show(Formation $formation)
    {
        return response()->json(
            [
                "message" => "Voici la formation",
                "formation" => $formation
            ]
        );
    }

    /**
     * @OA\Get(
     *     path="/api/formations/candidature_refuser/{formation}",
     *     summary="Voir les candidatures refuser pour une formation",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *   @OA\Parameter(in="path", name="formation", @OA\Schema(type="string")),
     *  security={
     *         {"BearerAuth": {}}
     *     },
     *   tags={"Formations"}
     * )
     */
    public function candidature_refuser(Formation $formation)
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        foreach ($formation->candidatures as $candidature) {
            if ($candidature->statut === "refuse") {
                $candidatures[] = $candidature;
            }
        }
        return response()->json([
            "message" => "Les candidatures de cette formations qui ont été refusé sont les suivants ",
            "candidatures" => $candidatures
        ]);
    }

    /**
     * @OA\Post(
     *     path="api/formation/{formation}",
     *     summary="Modifier une formation",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     
     *     @OA\Parameter(in="path", name="formation", @OA\Schema(type="string")),
     * *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             example={
     *                 "libelle": "string",
     *                 "description": "string",
     *                 "dateCloture": "string",
     *                 "dateDebut": "string",
     *                 "duree": "string",
     *                 "image": "string"
     *             }
     *         )
     *     ),
     *  security={
     *         {"BearerAuth": {}}
     *     },
     *   tags={"Formations"}
     * )
     */
    public function update(Request $request, Formation $formation)
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $validator = Validator::make(
            $request->all(),
            [
                'libelle' => ['required', 'string'],
                'description' => ['required', 'string'],
                'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
                'dateCloture' => 'required|date|after:now',
                'dateDebut' => 'required|date|after:dateCloture',
                'duree' => 'required|string',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
            $formation->update(
                ['image' => $imagePath,]
            );
        }
        $formation->update(
            [
                'libelle' => $request->libelle,
                'description' => $request->description,
                'dateCloture' => $request->dateCloture,
                'dateDebut' => $request->dateDebut,
                'duree' => $request->duree,
                'user_id' => auth()->user()->id
            ]
        );
        return response()->json([
            'message' => 'La formation a bien été modifiée',
            'data' => $formation
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/formation/{formation}",
     *     summary="Supprimer une formation via son id",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *
     *     @OA\Parameter(in="path", name="formation", @OA\Schema(type="string")),
     *     security={
     *         {"BearerAuth": {}}
     *     },
     *   tags={"Formations"}
     * )
     */
    public function destroy(Formation $formation)
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $formation->delete();
        return response()->json([
            'message' => 'La formation a bien été supprimée'
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/candidats/{formation}",
     *     summary="Obtenir les candidats d'une formations",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *     @OA\Response(response="404", description="Candidat non trouvée", @OA\JsonContent(example="")),
     *   @OA\Parameter(in="path", name="formation", @OA\Schema(type="string")),
     *  security={
     *         {"BearerAuth": {}}
     *     },
     *   tags={"Formations"}
     * )
     */
    public function candidats_formation(Formation $formation)
    {
        //recupère les formations liées à l'utilisateur connecté et ses candidatures associés
        $candidatures = $formation->candidatures;

        foreach ($candidatures as $candidature) {
            $candidats[] = $candidature->user;
        }
        return response()->json([
            "message" => "La liste des candidats pour une formations",
            "candidats" => $candidats
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/archive_formation/{formation}",
     *     summary="Archiver une formation",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *   @OA\Parameter(in="path", name="formation", @OA\Schema(type="string")),
     *  security={
     *         {"BearerAuth": {}}
     *     },
     *   tags={"Formations"}
     * )
     */
    public function archive(Formation $formation)
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $formation->is_deleted = true;
        $formation->save();
        return response()->json(
            [
                "message" => "La formation a bien été archivée",
                "formation" => $formation
            ]
        );
    }
    /**
     * @OA\Get(
     *     path="/api/cloture_formation/{formation}",
     *     summary="Clôturer une formation",
     *     @OA\Response(response="200", description="Succès", @OA\JsonContent(example="")),
     *   @OA\Parameter(in="path", name="formation", @OA\Schema(type="string")),
     *   security={
     *         {"BearerAuth": {}}
     *     },
     *   tags={"Formations"}
     * )
     */
    public function cloturer(Formation $formation)
    {
        if (auth()->user()->role_id == 2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $formation->cloturer_ou_pas = true;
        $formation->save();
        return response()->json(
            [
                "message" => "La formation a bien été cloturée",
                "formation" => $formation
            ]
        );
    }
}
