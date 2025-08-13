<?php

namespace App\Http\Controllers;

use App\Models\Interaction;
use App\Models\Ame;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InteractionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Interaction::query();

            // Filtres
            if ($request->has('ame_id')) {
                $query->where('ame_id', $request->ame_id);
            }
            if ($request->has('user_id')) {
                $query->where('user_id', $request->user_id);
            }
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('date_interaction', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            $interactions = $query->with(['ame', 'user'])->get();

            return response()->json([
                'status' => true,
                'message' => 'Liste des interactions récupérée avec succès',
                'data' => $interactions,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des interactions',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ame_id' => 'required|exists:ames,id',
                'user_id' => 'required|exists:users,id',
                'type' => 'required|in:appel,visite,priere,etude_biblique',
                'note' => 'nullable|string',
                'date_interaction' => 'required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            // Vérification que l'utilisateur est bien l'encadreur de l'âme
            $ame = Ame::find($request->ame_id);
            if ($ame->assigne_a && $ame->assigne_a != $request->user_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Seul l\'encadreur assigné peut créer une interaction pour cette âme',
                    'data' => [],
                ], 403);
            }

            $interaction = Interaction::create($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Interaction créée avec succès',
                'data' => $interaction,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la création de l\'interaction',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $interaction = Interaction::with(['ame', 'user'])->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Interaction récupérée avec succès',
                'data' => $interaction,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Interaction non trouvée',
                'error' => $e->getMessage(),
                'data' => [],
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $interaction = Interaction::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'ame_id' => 'sometimes|required|exists:ames,id',
                'user_id' => 'sometimes|required|exists:users,id',
                'type' => 'sometimes|required|in:appel,visite,priere,etude_biblique',
                'note' => 'nullable|string',
                'date_interaction' => 'sometimes|required|date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            // Vérification des permissions
            if ($request->has('ame_id') || $request->has('user_id')) {
                $ameId = $request->ame_id ?? $interaction->ame_id;
                $userId = $request->user_id ?? $interaction->user_id;

                $ame = Ame::find($ameId);
                if ($ame->assigne_a && $ame->assigne_a != $userId) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Seul l\'encadreur assigné peut être associé à cette interaction',
                        'data' => [],
                    ], 403);
                }
            }

            $interaction->update($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Interaction mise à jour avec succès',
                'data' => $interaction,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la mise à jour de l\'interaction',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $interaction = Interaction::findOrFail($id);
            $interaction->delete();

            return response()->json([
                'status' => true,
                'message' => 'Interaction supprimée avec succès',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la suppression de l\'interaction',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
