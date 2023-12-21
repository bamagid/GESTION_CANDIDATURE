<?php

namespace App\Http\Controllers;

use App\Mail\DemandeDeCandidatureAccepter;
use App\Mail\DemandeDeCandidatureRefuser;
use App\Models\Candidature;
use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class CandidatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->role_id==2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }

        $candidatures=Candidature::all();
        return response()->json([
            "message"=>"Voici la listes de toutes les candidatures",
            "candidatures"=>$candidatures
        ]);
    }

    public function userCandidatures(Request $request){
        if (auth()->user()->role_id==1) {
            return response()->json(['message' => 'Non autorisé. Seuls les candidats peuvent voir leurs candidatures.'], 403);
        }

        foreach ($request->user()->candidatures as $candidature) {
            $candidatures[]=$candidature;
        }
        return response()->json([
            'message'=>'Voici vos candidatures',
            'candidatures'=>$candidatures
        ]);
    }

    
    public function accepter()
    {
        if (auth()->user()->role_id==2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $candidatures=Candidature::where('statut','accepte')->get();
        return response()->json([
            "message"=>"Voici la listes de toutes les candidatures qui on été accepté",
            "candidatures"=>$candidatures
        ]);
    }
    public function en_attente()
    {
        if (auth()->user()->role_id==2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }                     
        $candidatures=Candidature::where('statut','en attente')->get();
        return response()->json([
            "message"=>"Voici la listes de toutes les candidatures qui sont en attente",
            "candidatures"=>$candidatures
        ]);
    }
    

    public function refuser()
    {
        if (auth()->user()->role_id==2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $candidatures=Candidature::where('statut','refuse')->get();
        return response()->json([
            "message"=>"Voici la listes de toutes les candidatures qui on été refusé",
            "candidatures"=>$candidatures
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
    public function store(Request $request ,Formation $formation)
    {
        // Vérifier si l'utilisateur a le rôle "candidat"
        if (auth()->user()->role_id==1) {
            return response()->json(['message' => 'Non autorisé. Seuls les candidats peuvent candidater.'], 403);
        }

        $candidature=Candidature::create([
            "user_id"=>$request->user()->id,
            "formation_id"=>$formation->id
        ]);
        return response()->json([
            "message"=>"La candidature a bien été pris en compte on vous enverras un message si elle a eté accepté ou refuser pour vous en informer",
            "candidature"=>$candidature
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Candidature $candidature)
    {   
        if (auth()->user()->role_id==2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        return response()->json([
            "message"=>"Voici la candidature que vous chercher",
            "candidature"=>$candidature
        ]);
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Candidature $candidature)
    {
        if (auth()->user()->role_id==2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $candidature->update([
            "statut"=>"refuse"  
        ]);
        Mail::to($candidature->user->email)->send(new DemandeDeCandidatureRefuser());
        return response()->json([
            "message"=>"Le refus de la candidature a reussi",
            "candidature"=>$candidature
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Candidature $candidature)
    {
        if (auth()->user()->role_id==2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $candidature->update([
            "statut"=>"accepte"  
        ]);
        Mail::to($candidature->user->email)->send(new DemandeDeCandidatureAccepter());

        return response()->json([
            "message"=>"La candidature a bien été accepté",
            "candidature"=>$candidature
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Candidature $candidature)
    {
        if (auth()->user()->role_id==2) {
            return response()->json(['message' => 'Vous n\'avez pas les droits pour faire cette action'], 403);
        }
        $candidature->delete();
        return response()->json([
            "message"=>"Cette candidature a été bien supprimer",
        ]);
    }
}
