<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZoneController extends Controller
{
    public function index()
    {
        try {
            $zones = Zone::withCount(['users', 'campagnes', 'cellules'])->get();

            return response()->json([
                'status' => true,
                'message' => 'Liste des zones récupérée avec succès',
                'data' => $zones,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des zones',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
    public function indexPublic()
{
    try {
        $zones = Zone::all(); // Ou toute autre logique de récupération

        return response()->json([
            'status' => true,
            'message' => 'Liste des zones récupérée avec succès',
            'data' => $zones
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Erreur lors de la récupération des zones',
            'error' => $e->getMessage()
        ], 500);
    }
}

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255|unique:zones,nom',
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

            $zone = Zone::create($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Zone créée avec succès',
                'data' => $zone,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la création de la zone',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $zone = Zone::with(['users', 'campagnes', 'cellules'])->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Zone récupérée avec succès',
                'data' => $zone,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Zone non trouvée',
                'error' => $e->getMessage(),
                'data' => [],
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $zone = Zone::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nom' => 'sometimes|required|string|max:255|unique:zones,nom,'.$zone->id,
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

            $zone->update($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Zone mise à jour avec succès',
                'data' => $zone,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la mise à jour de la zone',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $zone = Zone::findOrFail($id);
            $zone->delete();

            return response()->json([
                'status' => true,
                'message' => 'Zone supprimée avec succès',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la suppression de la zone',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
