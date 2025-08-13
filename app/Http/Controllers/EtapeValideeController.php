<?php

namespace App\Http\Controllers;

use App\Models\EtapeValidee;
use App\Models\Ame;
use App\Models\ParcoursSpirituel;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EtapeValideeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = EtapeValidee::query();

            // Filtres
            if ($request->has('ame_id')) {
                $query->where('ame_id', $request->ame_id);
            }
            if ($request->has('parcours_spirituel_id')) {
                $query->where('parcours_spirituel_id', $request->parcours_spirituel_id);
            }
            if ($request->has('valide_par')) {
                $query->where('valide_par', $request->valide_par);
            }
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('date_validation', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            $etapes = $query->get();

            return response()->json([
                'status' => true,
                'message' => 'Liste des étapes validées récupérée avec succès',
                'data' => $etapes,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des étapes validées',
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
                'parcours_spirituel_id' => 'required|exists:parcours_spirituels,id',
                'valide_par' => 'nullable|exists:users,id',
                'date_validation' => 'required|date',
                'commentaires' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            // Vérifier que l'âme n'a pas déjà validé ce parcours
            $exists = EtapeValidee::where('ame_id', $request->ame_id)
                ->where('parcours_spirituel_id', $request->parcours_spirituel_id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cette âme a déjà validé ce parcours spirituel',
                    'data' => [],
                ], 409);
            }

            // Vérifier que le validateur est bien l'encadreur de l'âme
            $ame = Ame::find($request->ame_id);
            if ($request->valide_par && $ame->assigne_a != $request->valide_par) {
                return response()->json([
                    'status' => false,
                    'message' => 'Seul l\'encadreur assigné peut valider cette étape',
                    'data' => [],
                ], 403);
            }

            $etapeValidee = EtapeValidee::create($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Étape validée créée avec succès',
                'data' => $etapeValidee,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la création de l\'étape validée',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $etapeValidee = EtapeValidee::with(['ame', 'parcours', 'validateur'])
                ->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Étape validée récupérée avec succès',
                'data' => $etapeValidee,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Étape validée non trouvée',
                'error' => $e->getMessage(),
                'data' => [],
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $etapeValidee = EtapeValidee::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'valide_par' => 'nullable|exists:users,id',
                'date_validation' => 'sometimes|required|date',
                'commentaires' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            // On ne permet pas de changer l'âme ou le parcours après création
            $data = $validator->validated();
            unset($data['ame_id'], $data['parcours_spirituel_id']);

            $etapeValidee->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Étape validée mise à jour avec succès',
                'data' => $etapeValidee,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la mise à jour de l\'étape validée',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $etapeValidee = EtapeValidee::findOrFail($id);
            $etapeValidee->delete();

            return response()->json([
                'status' => true,
                'message' => 'Étape validée supprimée avec succès',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la suppression de l\'étape validée',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
