<?php

namespace App\Http\Controllers;

use App\Models\Cellule;
use App\Models\Zone;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CelluleController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Cellule::query();

            // Filtres
            if ($request->has('zone_id')) {
                $query->where('zone_id', $request->zone_id);
            }
            if ($request->has('responsable_id')) {
                $query->where('responsable_id', $request->responsable_id);
            }
            if ($request->has('sans_responsable') && $request->sans_responsable) {
                $query->whereNull('responsable_id');
            }

            $cellules = $query->with(['zone', 'responsable', 'ames'])->get();

            return response()->json([
                'status' => true,
                'message' => 'Liste des cellules récupérée avec succès',
                'data' => $cellules,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des cellules',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255|unique:cellules,nom',
                'zone_id' => 'required|exists:zones,id',
                'responsable_id' => 'nullable|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            // Vérification que le responsable est bien dans la même zone
            if ($request->responsable_id) {
                $responsable = User::find($request->responsable_id);
                if ($responsable->zone_id != $request->zone_id) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Le responsable doit appartenir à la même zone que la cellule',
                        'data' => [],
                    ], 422);
                }
            }

            $cellule = Cellule::create($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Cellule créée avec succès',
                'data' => $cellule,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la création de la cellule',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $cellule = Cellule::with(['zone', 'responsable', 'ames'])->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Cellule récupérée avec succès',
                'data' => $cellule,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Cellule non trouvée',
                'error' => $e->getMessage(),
                'data' => [],
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $cellule = Cellule::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nom' => 'sometimes|required|string|max:255|unique:cellules,nom,'.$cellule->id,
                'zone_id' => 'sometimes|required|exists:zones,id',
                'responsable_id' => 'nullable|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            // Vérification cohérence zone/responsable
            if ($request->has('responsable_id') || $request->has('zone_id')) {
                $zoneId = $request->zone_id ?? $cellule->zone_id;
                $responsableId = $request->responsable_id ?? $cellule->responsable_id;

                if ($responsableId) {
                    $responsable = User::find($responsableId);
                    if ($responsable->zone_id != $zoneId) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Le responsable doit appartenir à la même zone que la cellule',
                            'data' => [],
                        ], 422);
                    }
                }
            }

            $cellule->update($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Cellule mise à jour avec succès',
                'data' => $cellule,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la mise à jour de la cellule',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $cellule = Cellule::findOrFail($id);
            $cellule->delete();

            return response()->json([
                'status' => true,
                'message' => 'Cellule supprimée avec succès',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la suppression de la cellule',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
