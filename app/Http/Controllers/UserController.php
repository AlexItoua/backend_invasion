<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Zone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = User::with('zone');

            // Ajout des filtres
            if ($request->has('role')) {
                $query->where('role', $request->role);
            }
            if ($request->has('zone_id')) {
                $query->where('zone_id', $request->zone_id);
            }
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            $users = $query->get();

            return response()->json([
                'status' => true,
                'message' => 'Liste des utilisateurs récupérée avec succès',
                'data' => $users,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des utilisateurs',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'telephone' => 'nullable|string|max:20',
                'role' => 'required|in:evangeliste,encadreur,admin,gagneur',
                'zone_id' => 'nullable|exists:zones,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            $validated = $validator->validated();
            $validated['password'] = Hash::make($validated['password']);
            $validated['is_active'] = true; // Par défaut actif

            $user = User::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Utilisateur créé avec succès',
                'data' => $user,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la création de l\'utilisateur',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::with('zone')->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Utilisateur récupéré avec succès',
                'data' => $user,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Utilisateur non trouvé',
                'error' => $e->getMessage(),
                'data' => [],
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nom' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
                'password' => 'sometimes|string|min:8',
                'telephone' => 'nullable|string|max:20',
                'role' => 'required|in:evangeliste,encadreur,admin,gagneur',
                'zone_id' => 'nullable|exists:zones,id',
                'is_active' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            $validated = $validator->validated();
            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Utilisateur mis à jour avec succès',
                'data' => $user,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la mise à jour de l\'utilisateur',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'Utilisateur supprimé avec succès',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la suppression de l\'utilisateur',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    // MÉTHODES POUR L'ACTIVATION/DÉSACTIVATION - AJOUT
    public function activate($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->activate();

            return response()->json([
                'status' => true,
                'message' => 'Utilisateur activé avec succès',
                'data' => $user,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de l\'activation de l\'utilisateur',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function deactivate($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->deactivate();

            return response()->json([
                'status' => true,
                'message' => 'Utilisateur désactivé avec succès',
                'data' => $user,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la désactivation de l\'utilisateur',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    // Méthode pour récupérer les utilisateurs actifs
    public function active(Request $request)
    {
        try {
            $users = User::active()->with('zone')->get();

            return response()->json([
                'status' => true,
                'message' => 'Utilisateurs actifs récupérés avec succès',
                'data' => $users,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des utilisateurs actifs',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    // Méthode de recherche
    public function search(Request $request)
    {
        try {
            $query = User::with('zone');

            if ($request->has('q')) {
                $searchTerm = $request->q;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nom', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('telephone', 'LIKE', "%{$searchTerm}%");
                });
            }

            $users = $query->get();

            return response()->json([
                'status' => true,
                'message' => 'Résultats de recherche récupérés avec succès',
                'data' => $users,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la recherche',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}