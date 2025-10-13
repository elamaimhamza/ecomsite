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

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $user = $request->get('auth_user'); // from middleware

        return response()->json([
            'user' => $user
        ]);
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

        $user = $request->get('auth_user'); // same method

        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:utilisateurs,email,' . $user->id,
            'mot_de_passe' => 'sometimes|min:6|confirmed',
            'adresse' => 'sometimes|string|max:255',
            'code_postal' => 'sometimes|string|max:20',
            'ville' => 'sometimes|string|max:100',
        ]);

        if (isset($validated['mot_de_passe'])) {
            $validated['mot_de_passe'] = bcrypt($validated['mot_de_passe']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Profil mis à jour avec succès.',
            'user' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Utilisateur $utilisateur)
    {
        //
    }
}
