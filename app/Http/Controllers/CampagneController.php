<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CampagneController extends Controller
{
    public function index()
    {
        try {
            $campagnes = Campagne::with(['zone', 'ames', 'statistiques'])->get();

            return response()->json([
                'status' => true,
                'message' => 'Liste des campagnes récupérée avec succès',
                'data' => $campagnes,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des campagnes',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255|unique:campagnes,nom',
                'date_debut' => 'required|date',
                'date_fin' => 'nullable|date|after_or_equal:date_debut',
                'zone_id' => 'required|exists:zones,id',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            $campagne = Campagne::create($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Campagne créée avec succès',
                'data' => $campagne,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la création de la campagne',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $campagne = Campagne::with(['zone', 'ames', 'statistiques'])->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Campagne récupérée avec succès',
                'data' => $campagne,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Campagne non trouvée',
                'error' => $e->getMessage(),
                'data' => [],
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $campagne = Campagne::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nom' => 'sometimes|required|string|max:255|unique:campagnes,nom,'.$campagne->id,
                'date_debut' => 'sometimes|required|date',
                'date_fin' => 'nullable|date|after_or_equal:date_debut',
                'zone_id' => 'sometimes|required|exists:zones,id',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            $campagne->update($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Campagne mise à jour avec succès',
                'data' => $campagne,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la mise à jour de la campagne',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $campagne = Campagne::findOrFail($id);
            $campagne->delete();

            return response()->json([
                'status' => true,
                'message' => 'Campagne supprimée avec succès',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la suppression de la campagne',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
