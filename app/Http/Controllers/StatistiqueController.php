<?php

namespace App\Http\Controllers;

use App\Models\Statistique;
use App\Models\Campagne;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Statistique::query();

            // Filtres
            if ($request->has('campagne_id')) {
                $query->where('campagne_id', $request->campagne_id);
            }
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('date_generation', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            $statistiques = $query->with('campagne')
                ->recentFirst()
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Liste des statistiques récupérée avec succès',
                'data' => $statistiques,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'campagne_id' => 'required|exists:campagnes,id',
                'total_ames' => 'required|integer|min:0',
                'baptises' => 'required|integer|min:0|lte:total_ames',
                'fidelises' => 'required|integer|min:0|lte:total_ames',
                'nouvelles_ames' => 'required|integer|min:0|lte:total_ames',
                'date_generation' => 'required|date|unique:statistiques,date_generation,NULL,id,campagne_id,' . $request->campagne_id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            // Vérification cohérence avec la campagne
            $campagne = Campagne::findOrFail($request->campagne_id);
            if (
                $request->date_generation < $campagne->date_debut ||
                ($campagne->date_fin && $request->date_generation > $campagne->date_fin)
            ) {
                return response()->json([
                    'status' => false,
                    'message' => 'La date de génération doit être dans la période de la campagne',
                    'data' => [],
                ], 422);
            }

            $statistique = Statistique::create($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Statistique créée avec succès',
                'data' => $statistique,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la création de la statistique',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $statistique = Statistique::with('campagne')->findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Statistique récupérée avec succès',
                'data' => $statistique,
                'rapport' => $statistique->genererRapport(),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Statistique non trouvée',
                'error' => $e->getMessage(),
                'data' => [],
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $statistique = Statistique::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'total_ames' => 'sometimes|required|integer|min:0',
                'baptises' => 'sometimes|required|integer|min:0|lte:total_ames',
                'fidelises' => 'sometimes|required|integer|min:0|lte:total_ames',
                'nouvelles_ames' => 'sometimes|required|integer|min:0|lte:total_ames',
                'date_generation' => 'sometimes|required|date|unique:statistiques,date_generation,' . $id . ',id,campagne_id,' . $statistique->campagne_id,
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            // Vérification cohérence avec la campagne si date modifiée
            if ($request->has('date_generation')) {
                $campagne = Campagne::findOrFail($statistique->campagne_id);
                if (
                    $request->date_generation < $campagne->date_debut ||
                    ($campagne->date_fin && $request->date_generation > $campagne->date_fin)
                ) {
                    return response()->json([
                        'status' => false,
                        'message' => 'La date de génération doit être dans la période de la campagne',
                        'data' => [],
                    ], 422);
                }
            }

            $statistique->update($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Statistique mise à jour avec succès',
                'data' => $statistique,
                'rapport' => $statistique->genererRapport(),
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la mise à jour de la statistique',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $statistique = Statistique::findOrFail($id);
            $statistique->delete();

            return response()->json([
                'status' => true,
                'message' => 'Statistique supprimée avec succès',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la suppression de la statistique',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    // Méthode supplémentaire pour générer un rapport consolidé
    public function rapportConsolide(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'campagne_id' => 'required|exists:campagnes,id',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            $query = Statistique::where('campagne_id', $request->campagne_id);

            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('date_generation', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            $stats = $query->get();

            $consolide = [
                'total_ames' => $stats->sum('total_ames'),
                'baptises' => $stats->sum('baptises'),
                'fidelises' => $stats->sum('fidelises'),
                'nouvelles_ames' => $stats->sum('nouvelles_ames'),
                'nombre_rapports' => $stats->count(),
            ];

            if ($consolide['total_ames'] > 0) {
                $consolide['taux_bapteme'] = round(($consolide['baptises'] / $consolide['total_ames']) * 100, 2);
                $consolide['taux_fidelisation'] = round(($consolide['fidelises'] / $consolide['total_ames']) * 100, 2);
                $consolide['taux_nouvelles_ames'] = round(($consolide['nouvelles_ames'] / $consolide['total_ames']) * 100, 2);
            } else {
                $consolide['taux_bapteme'] = 0;
                $consolide['taux_fidelisation'] = 0;
                $consolide['taux_nouvelles_ames'] = 0;
            }

            return response()->json([
                'status' => true,
                'message' => 'Rapport consolidé généré avec succès',
                'data' => $consolide,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la génération du rapport',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
    // Pour les stats hebdomadaires
    public function statsHebdomadaires(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Si aucune date fournie, prendre la semaine actuelle
            $start_date = $request->start_date ?? now()->startOfWeek()->toDateString();
            $end_date = $request->end_date ?? now()->endOfWeek()->toDateString();

            $stats = DB::table('ames')
                ->select(
                    DB::raw('WEEK(created_at, 1) as semaine'),
                    DB::raw('COUNT(*) as nouvelles_ames'),
                    DB::raw('SUM(CASE WHEN suivi = true THEN 1 ELSE 0 END) as ames_suivies')
                )
                ->whereBetween('created_at', [$start_date, $end_date])
                ->groupBy('semaine')
                ->orderBy('semaine')
                ->get();

            return response()->json([
                'status' => true,
                'data' => [
                    'semaines' => $stats->pluck('semaine')->map(fn($s) => "S$s"),
                    'nouvelles_ames' => $stats->pluck('nouvelles_ames'),
                    'ames_suivies' => $stats->pluck('ames_suivies')
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors du calcul des stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // Pour les conversions mensuelles
    public function statsMensuelles(Request $request)
    {
        try {
            // Utiliser l'année fournie ou l'année courante par défaut
            $year = $request->year ?? now()->year;

            // Validation de l'année
            $validator = Validator::make(['year' => $year], [
                'year' => 'required|integer|min:2000|max:2100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Base de la requête
            $query = DB::table('ames')
                ->select(
                    DB::raw('MONTH(created_at) as mois'),
                    DB::raw('COUNT(*) as conversions')
                )
                ->whereYear('created_at', $year);

            // Filtre optionnel par campagne
            if ($request->has('campagne_id')) {
                $query->where('campagne_id', $request->campagne_id);
            }

            $stats = $query
                ->groupBy('mois')
                ->orderBy('mois')
                ->get()
                ->keyBy('mois');

            // Tableau des mois en français
            $moisFr = [
                1 => 'Jan',
                2 => 'Fév',
                3 => 'Mar',
                4 => 'Avr',
                5 => 'Mai',
                6 => 'Juin',
                7 => 'Juil',
                8 => 'Aoû',
                9 => 'Sep',
                10 => 'Oct',
                11 => 'Nov',
                12 => 'Déc'
            ];

            $labels = [];
            $values = [];

            for ($m = 1; $m <= 12; $m++) {
                $labels[] = $moisFr[$m];
                $values[] = isset($stats[$m]) ? (int)$stats[$m]->conversions : 0;
            }

            return response()->json([
                'status' => true,
                'data' => [
                    'mois' => $labels,
                    'conversions' => $values
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors du calcul des stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}