<?php

namespace App\Http\Controllers;

use App\Models\EtapeValidee;
use App\Models\EtapeParcours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class EtapeValideeController extends Controller
{
    /**
     * Afficher toutes les étapes validées avec filtres possibles
     */
    public function index(Request $request)
    {
        try {
            $query = EtapeValidee::with(['parcoursAme', 'etape']);

            if ($request->has('parcours_ame_id')) {
                $query->where('parcours_ame_id', $request->parcours_ame_id);
            }

            if ($request->has('etape_parcours_id')) {
                $query->where('etape_parcours_id', $request->etape_parcours_id);
            }

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('date_validation', [$request->start_date, $request->end_date]);
            }

            $etapes = $query->get();

            return response()->json([
                'status'  => true,
                'message' => 'Liste des étapes validées récupérée avec succès',
                'data'    => $etapes,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Erreur lors de la récupération des étapes validées',
                'error'   => $e->getMessage(),
                'data'    => [],
            ], 500);
        }
    }

    /**
     * Créer une nouvelle étape validée
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'parcours_ame_id'   => 'required|exists:parcours_ames,id',
                'etape_parcours_id' => 'required|exists:etape_parcours,id',
                'date_validation'   => 'required|date',
                'notes'             => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Erreur de validation',
                    'errors'  => $validator->errors(),
                    'data'    => [],
                ], 422);
            }

            // Vérifier si déjà validée
            $exists = EtapeValidee::where('parcours_ame_id', $request->parcours_ame_id)
                ->where('etape_parcours_id', $request->etape_parcours_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Cette étape est déjà validée pour ce parcours',
                    'data'    => [],
                ], 409);
            }

            $etapeValidee = EtapeValidee::create($validator->validated());

            return response()->json([
                'status'  => true,
                'message' => 'Étape validée créée avec succès',
                'data'    => $etapeValidee,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Erreur lors de la création de l\'étape validée',
                'error'   => $e->getMessage(),
                'data'    => [],
            ], 500);
        }
    }

    /**
     * Afficher une étape validée spécifique
     */
    public function show($id)
    {
        try {
            $etapeValidee = EtapeValidee::with(['parcoursAme', 'etape'])->findOrFail($id);

            return response()->json([
                'status'  => true,
                'message' => 'Étape validée récupérée avec succès',
                'data'    => $etapeValidee,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Étape validée non trouvée',
                'data'    => [],
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Erreur lors de la récupération de l\'étape validée',
                'error'   => $e->getMessage(),
                'data'    => [],
            ], 500);
        }
    }

    /**
     * Mettre à jour une étape validée
     */
    public function update(Request $request, $id)
    {
        try {
            $etapeValidee = EtapeValidee::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'date_validation' => 'sometimes|required|date',
                'notes'           => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Erreur de validation',
                    'errors'  => $validator->errors(),
                    'data'    => [],
                ], 422);
            }

            $etapeValidee->update($validator->validated());

            return response()->json([
                'status'  => true,
                'message' => 'Étape validée mise à jour avec succès',
                'data'    => $etapeValidee,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Étape validée non trouvée',
                'data'    => [],
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Erreur lors de la mise à jour de l\'étape validée',
                'error'   => $e->getMessage(),
                'data'    => [],
            ], 500);
        }
    }

    /**
     * Supprimer une étape validée (soft delete)
     */
    public function destroy($id)
    {
        try {
            $etapeValidee = EtapeValidee::findOrFail($id);
            $etapeValidee->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Étape validée supprimée avec succès',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Étape validée non trouvée',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Erreur lors de la suppression de l\'étape validée',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
