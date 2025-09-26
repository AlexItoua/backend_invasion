<?php

namespace App\Http\Controllers;

use App\Models\EtapeParcours;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EtapeParcoursController extends Controller
{
    /**
     * Afficher toutes les étapes de parcours
     */
    public function index(): JsonResponse
    {
        $etapes = EtapeParcours::with('parcours')->get();

        return response()->json([
            'success' => true,
            'data' => $etapes
        ]);
    }

    /**
     * Créer une nouvelle étape de parcours
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'parcours_spirituel_id' => 'required|exists:parcours_spirituels,id',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'contenu' => 'nullable|string',
            'ordre' => 'integer|min:1',
            'duree_estimee_minutes' => 'nullable|integer|min:1',
            'est_actif' => 'boolean',
        ]);

        $etape = EtapeParcours::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Étape créée avec succès.',
            'data' => $etape
        ], 201);
    }

    /**
     * Afficher une étape spécifique
     */
    public function show($id): JsonResponse
    {
        try {
            $etape = EtapeParcours::with(['parcours', 'etapesValidees'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $etape
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => "Étape non trouvée."
            ], 404);
        }
    }

    /**
     * Mettre à jour une étape de parcours
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $etape = EtapeParcours::findOrFail($id);

            $validated = $request->validate([
                'parcours_spirituel_id' => 'sometimes|exists:parcours_spirituels,id',
                'titre' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'contenu' => 'nullable|string',
                'ordre' => 'integer|min:1',
                'duree_estimee_minutes' => 'nullable|integer|min:1',
                'est_actif' => 'boolean',
            ]);

            $etape->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Étape mise à jour avec succès.',
                'data' => $etape
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => "Étape non trouvée."
            ], 404);
        }
    }

    /**
     * Supprimer une étape de parcours (soft delete)
     */
    public function destroy($id): JsonResponse
    {
        try {
            $etape = EtapeParcours::findOrFail($id);
            $etape->delete();

            return response()->json([
                'success' => true,
                'message' => "Étape supprimée avec succès."
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => "Étape non trouvée."
            ], 404);
        }
    }
}
