<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TacheController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Tache::query()->with(['user', 'ame']);

            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            if ($request->has('ame_id')) {
                $query->where('ame_id', $request->ame_id);
            }
            if ($request->has('statut')) {
                $query->where('statut', $request->statut);
            }
            if ($request->has('priorite')) {
                $query->where('priorite', $request->priorite);
            }

            $taches = $query->get();

            return response()->json([
                'status' => true,
                'message' => 'Liste des tâches récupérée avec succès',
                'data' => $taches,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des tâches',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Debug: Log les données reçues
            Log::debug('Received request data:', $request->all());

            $validator = Validator::make($request->all(), [
                'titre' => 'required|string|max:255',
                'description' => 'required|string',
                'user_id' => 'required|exists:users,id',
                'ame_id' => 'nullable|exists:ames,id',
                'echeance' => 'required|date',
                'statut' => 'required|in:en_attente,terminee,annulee',
                'priorite' => 'required|in:basse,normale,haute',
            ]);

            if ($validator->fails()) {
                // Debug: Log les erreurs de validation
                Log::error('Validation errors:', $validator->errors()->toArray());

                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Gestion robuste des formats de date
            $validated = $validator->validated();

            // Convertir les dates de différents formats
            if (is_string($validated['echeance'])) {
                try {
                    // Essayer de parser la date
                    $validated['echeance'] = \Carbon\Carbon::parse($validated['echeance']);
                } catch (\Exception $e) {
                    Log::error('Date parsing error', [
                        'input' => $validated['echeance'],
                        'error' => $e->getMessage()
                    ]);

                    return response()->json([
                        'status' => false,
                        'message' => 'Format de date invalide',
                        'errors' => ['echeance' => ['Le format de date est invalide. Utilisez YYYY-MM-DD ou ISO 8601']],
                    ], 422);
                }
            }

            $tache = Tache::create($validated);

            return response()->json([
                'status' => true,
                'message' => 'Tâche créée avec succès',
                'data' => $tache,
            ], 201);
        } catch (Exception $e) {
            Log::error('Error creating task: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la création de la tâche',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function recentes(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);

            $taches = Tache::orderBy('created_at', 'desc')
                ->take($limit)
                ->with(['user', 'ame'])
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Dernières tâches récupérées avec succès',
                'data' => $taches,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des tâches récentes',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $tache = Tache::with(['user', 'ame'])->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Tâche récupérée avec succès',
                'data' => $tache,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Tâche non trouvée',
                'error' => $e->getMessage(),
                'data' => [],
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $tache = Tache::findOrFail($id);

            // Debug: Log les données reçues
            Log::debug('Received update data:', $request->all());

            $validator = Validator::make($request->all(), [
                'titre' => 'required|string|max:255',
                'description' => 'required|string',
                'user_id' => 'required|exists:users,id',
                'ame_id' => 'nullable|exists:ames,id',
                'echeance' => 'required|date',
                'statut' => 'required|in:en_attente,terminee,annulee',
                'priorite' => 'required|in:basse,normale,haute',
            ]);

            if ($validator->fails()) {
                // Debug: Log les erreurs de validation
                Log::error('Validation errors on update:', $validator->errors()->toArray());

                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Gestion robuste des formats de date
            $validated = $validator->validated();

            // Convertir les dates de différents formats
            if (is_string($validated['echeance'])) {
                try {
                    // Essayer de parser la date
                    $validated['echeance'] = \Carbon\Carbon::parse($validated['echeance']);
                } catch (\Exception $e) {
                    Log::error('Date parsing error in update', [
                        'input' => $validated['echeance'],
                        'error' => $e->getMessage()
                    ]);

                    return response()->json([
                        'status' => false,
                        'message' => 'Format de date invalide',
                        'errors' => ['echeance' => ['Le format de date est invalide. Utilisez YYYY-MM-DD ou ISO 8601']],
                    ], 422);
                }
            }

            $tache->update($validated);

            return response()->json([
                'status' => true,
                'message' => 'Tâche mise à jour avec succès',
                'data' => $tache,
            ]);
        } catch (Exception $e) {
            Log::error('Error updating task: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la mise à jour de la tâche',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $tache = Tache::findOrFail($id);
            $tache->delete();

            return response()->json([
                'status' => true,
                'message' => 'Tâche supprimée avec succès',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la suppression de la tâche',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
