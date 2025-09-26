<?php

namespace App\Http\Controllers;

use App\Models\ParcoursSpirituel;
use App\Models\ParcoursAme;
use App\Models\ParcoursAmes;
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
                ->with(['etapes'])
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
            $parcours = ParcoursSpirituel::with(['etapes', 'parcoursAmes'])->findOrFail($id);

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
                'nom' => 'sometimes|required|string|max:255|unique:parcours_spirituels,nom,' . $parcours->id,
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

    public function demarrerParcours(Request $request, $id)
    {
        try {
            $parcours = ParcoursSpirituel::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'ame_id' => 'required|exists:ames,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            // Vérifier si l'âme n'a pas déjà ce parcours en cours
            $parcoursExistant = ParcoursAmes::where('ame_id', $request->ame_id)
                ->where('parcours_spirituel_id', $id)
                ->whereIn('statut', ['en_cours', 'termine'])
                ->first();

            if ($parcoursExistant) {
                return response()->json([
                    'status' => false,
                    'message' => 'Ce parcours est déjà en cours ou terminé pour cette âme',
                    'data' => $parcoursExistant,
                ], 409);
            }

            $parcoursAme = ParcoursAmes::create([
                'ame_id' => $request->ame_id,
                'parcours_spirituel_id' => $id,
                'date_debut' => now(),
                'statut' => 'en_cours',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Parcours démarré avec succès',
                'data' => $parcoursAme, // Retourner le parcoursAme créé, pas le parcours spirituel
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors du démarrage du parcours',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function progression($id, $ameId)
    {
        try {
            $parcours = ParcoursSpirituel::findOrFail($id);
            $progression = $parcours->progressionPourAme($ameId);

            return response()->json([
                'status' => true,
                'message' => 'Progression récupérée avec succès',
                'data' => $progression,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération de la progression',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
