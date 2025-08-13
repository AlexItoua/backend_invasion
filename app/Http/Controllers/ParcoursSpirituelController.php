<?php

namespace App\Http\Controllers;

use App\Models\ParcoursSpirituel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParcoursSpirituelController extends Controller
{
    public function index()
    {
        try {
            $parcours = ParcoursSpirituel::actifs()
                ->ordonnes()
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Liste des parcours spirituels récupérée avec succès',
                'data' => $parcours,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des parcours',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255|unique:parcours_spirituels,nom',
                'description' => 'nullable|string',
                'ordre' => 'required|integer|min:1',
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

            $parcours = ParcoursSpirituel::create($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Parcours spirituel créé avec succès',
                'data' => $parcours,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la création du parcours',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $parcours = ParcoursSpirituel::with(['etapesValidees'])->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Parcours spirituel récupéré avec succès',
                'data' => $parcours,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Parcours spirituel non trouvé',
                'error' => $e->getMessage(),
                'data' => [],
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $parcours = ParcoursSpirituel::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nom' => 'sometimes|required|string|max:255|unique:parcours_spirituels,nom,'.$parcours->id,
                'description' => 'nullable|string',
                'ordre' => 'sometimes|required|integer|min:1',
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

            $parcours->update($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Parcours spirituel mis à jour avec succès',
                'data' => $parcours,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la mise à jour du parcours',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $parcours = ParcoursSpirituel::findOrFail($id);
            $parcours->delete();

            return response()->json([
                'status' => true,
                'message' => 'Parcours spirituel supprimé avec succès',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la suppression du parcours',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
