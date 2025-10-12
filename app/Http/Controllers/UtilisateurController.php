<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UtilisateurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:utilisateurs,email',
            'mot_de_passe' => 'required|string|min:6',
            'adresse' => 'required|string|max:255',
            'code_postal' => 'required|string|max:20',
            'ville' => 'required|string|max:100',
        ]);

        $utilisateur = Utilisateur::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'mot_de_passe' => Hash::make($validated['mot_de_passe']),
            'adresse' => $validated['adresse'],
            'code_postal' => $validated['code_postal'],
            'ville' => $validated['ville'],
            'type_utilisateur' => 'Membre', // default value
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'data' => [
                'id' => $utilisateur->id,
                'nom' => $utilisateur->nom,
                'prenom' => $utilisateur->prenom,
                'email' => $utilisateur->email,
                'type_utilisateur' => $utilisateur->type_utilisateur,
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        // ✅ Validate input
        $validated = $request->validate([
            'email' => 'required|email',
            'mot_de_passe' => 'required|string',
        ]);

        // ✅ Find the user by email
        $utilisateur = Utilisateur::where('email', $validated['email'])->first();

        if (!$utilisateur) {
            return response()->json(['message' => 'Email introuvable'], 404);
        }

        // ✅ Check password
        if (!Hash::check($validated['mot_de_passe'], $utilisateur->mot_de_passe)) {
            return response()->json(['message' => 'Mot de passe incorrect'], 401);
        }

        // ✅ Generate new API token
        $token = Str::random(60);
        $utilisateur->update(['api_token' => hash('sha256', $token)]);

        // ✅ Return token and basic info
        return response()->json([
            'message' => 'Connexion réussie',
            'data' => [
                'id' => $utilisateur->id,
                'nom' => $utilisateur->nom,
                'prenom' => $utilisateur->prenom,
                'email' => $utilisateur->email,
                'type_utilisateur' => $utilisateur->type_utilisateur,
                'api_token' => $token // client stores this
            ]
        ]);
    }

    public function verify(Request $request)
    {
        $token = $request->input("api_token");

        $hashed = hash('sha256', $token);

        $utilisateur = Utilisateur::where('api_token', $hashed)->first();
        if (!$utilisateur) {
            return response()->json(["message" => "token invalide"], 401);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Token valide',
            'data' => [
                'id' => $utilisateur->id,
                'nom' => $utilisateur->nom,
                'prenom' => $utilisateur->prenom,
                'email' => $utilisateur->email,
                'type_utilisateur' => $utilisateur->type_utilisateur,
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Utilisateur $utilisateur)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Utilisateur $utilisateur)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Utilisateur $utilisateur)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Utilisateur $utilisateur)
    {
        //
    }
}
