<?php

namespace App\Http\Controllers;

use App\Models\ParcoursAme;
use App\Models\ParcoursSpirituel;
use App\Models\EtapeValidee;
use App\Models\ParcoursAmes;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParcoursAmesController extends Controller
{
    public function validerEtape(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'parcours_ame_id' => 'required|exists:parcours_ames,id',
                'etape_parcours_id' => 'required|exists:etape_parcours,id',

                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            // Vérifier si l'étape appartient au même parcours
            $parcoursAme = ParcoursAmes::findOrFail($request->parcours_ame_id);
            $etapeParcours = $parcoursAme->parcours->etapes()
                ->where('id', $request->etape_parcours_id)
                ->first();

            if (!$etapeParcours) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cette étape ne fait pas partie du parcours en cours',
                    'data' => [],
                ], 422);
            }

            // Vérifier si l'étape n'a pas déjà été validée
            $etapeValideeExistante = EtapeValidee::where('parcours_ame_id', $request->parcours_ame_id)
                ->where('etape_parcours_id', $request->etape_parcours_id)
                ->first();

            if ($etapeValideeExistante) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cette étape a déjà été validée',
                    'data' => $etapeValideeExistante,
                ], 409);
            }

            $etapeValidee = EtapeValidee::create([
                'parcours_ame_id' => $request->parcours_ame_id,
                'etape_parcours_id' => $request->etape_parcours_id,
                'date_validation' => now(),
                'notes' => $request->notes,
            ]);

            // Vérifier si c'est la dernière étape pour terminer le parcours
            $etapesValidees = $parcoursAme->etapesValidees()->count();
            $totalEtapes = $parcoursAme->parcours->etapes()->count();

            if ($etapesValidees >= $totalEtapes) {
                $parcoursAme->update([
                    'statut' => 'termine',
                    'date_fin' => now()
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Étape validée avec succès',
                'data' => $etapeValidee,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la validation de l\'étape',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function parcoursEnCours($ameId)
    {
        try {
            $parcoursEnCours = ParcoursAmes::with(['parcours', 'etapesValidees.etape'])
                ->where('ame_id', $ameId)
                ->where('statut', 'en_cours')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Parcours en cours récupérés avec succès',
                'data' => $parcoursEnCours,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des parcours en cours',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
