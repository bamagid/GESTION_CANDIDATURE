<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formations = Formation::where('is_deleted', false)->get;
        return response()->json([
            'status' => true,
            "message" => "Voici la liste des formations disponible",
            'formations' => $formations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function candidature_accepter(Formation $formation)
    {
        foreach ($formation->candidatures as $candidature) {
            if ($candidature->statut === "accepte") {
                return $candidature;
            }
        }

        return response()->json([
            "message" => "Les candidatures de cette formations qui ont été accepté sont les suivants ",
            "candidature" => $candidature
        ]);
    }
    public function candidature_en_attente(Formation $formation)
    {
        foreach ($formation->candidatures as $candidature) {
            if ($candidature->statut === "en attente") {
                return $candidature;
            }
        }

        return response()->json([
            "message" => "Les candidatures de cette formations qui sont en attente sont les suivants ",
            "candidature" => $candidature
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
     * Display the specified resource.
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
     * Show the form for editing the specified resource.
     */
    public function candidature_refuser(Formation $formation)
    {
        foreach ($formation->candidatures as $candidature) {
            if ($candidature->statut === "refuse") {
                return $candidature;
            }
        }
        return response()->json([
            "message" => "Les candidatures de cette formations qui ont été refusé sont les suivants ",
            "candidature" => $candidature
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Formation $formation)
    {
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
     * Remove the specified resource from storage.
     */
    public function destroy(Formation $formation)
    {
        $formation->delete();
        return response()->json([
            'message' => 'La formation a bien été supprimée'
        ]);
    }
    public function archive(Formation $formation)
    {
        $formation->is_deleted = true;
        $formation->save();
        return response()->json(
            [
                "message" => "La formation a bien été archivée",
                "formation" => $formation
            ]
        );
    }
    public function cloturer(Formation $formation)
    {
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
