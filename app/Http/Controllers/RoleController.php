<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        try {
            $roles = Role::actifs()
                        ->withCount('users')
                        ->get();

            return response()->json([
                'status' => true,
                'message' => 'Liste des rôles récupérée avec succès',
                'data' => $roles,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des rôles',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255|unique:roles,nom',
                'description' => 'nullable|string',
                'permissions' => 'nullable|json',
                'est_actif' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            $role = Role::create($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Rôle créé avec succès',
                'data' => $role,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la création du rôle',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $role = Role::with(['users'])->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Rôle récupéré avec succès',
                'data' => $role,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Rôle non trouvé',
                'error' => $e->getMessage(),
                'data' => [],
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nom' => 'sometimes|required|string|max:255|unique:roles,nom,'.$role->id,
                'description' => 'nullable|string',
                'permissions' => 'nullable|json',
                'est_actif' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            $role->update($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Rôle mis à jour avec succès',
                'data' => $role,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la mise à jour du rôle',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);

            // Empêcher la suppression des rôles système
            if (in_array($role->nom, [Role::ADMIN, Role::ENCADREUR, Role::EVANGELISTE])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Impossible de supprimer un rôle système',
                    'data' => [],
                ], 403);
            }

            $role->delete();

            return response()->json([
                'status' => true,
                'message' => 'Rôle supprimé avec succès',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la suppression du rôle',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    // Méthode supplémentaire pour gérer les permissions
    public function updatePermissions(Request $request, $id)
    {
        try {
            $role = Role::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'permissions' => 'required|array',
                'permissions.*' => 'string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            $role->update(['permissions' => $request->permissions]);

            return response()->json([
                'status' => true,
                'message' => 'Permissions mises à jour avec succès',
                'data' => $role,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la mise à jour des permissions',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
