<?php

namespace App\Http\Controllers;

use App\Models\Ame;
use App\Models\Campagne;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AmeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Ame::query();

            if ($request->has('campagne_id')) {
                $query->where('campagne_id', $request->campagne_id);
            }
            if ($request->has('assigne_a')) {
                $query->where('assigne_a', $request->assigne_a);
            }
            if ($request->has('cellule_id')) {
                $query->where('cellule_id', $request->cellule_id);
            }
            if ($request->has('sexe')) {
                $query->where('sexe', $request->sexe);
            }

            $ames = $query->get();

            return response()->json([
                'status' => true,
                'message' => 'Liste des âmes récupérée avec succès',
                'data' => $ames,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des âmes',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255',
                'telephone' => 'nullable|string|max:20',
                'sexe' => 'required|in:H,F',
                'age' => 'nullable|integer|min:0',
                'adresse' => 'nullable|string',
                'image' => 'nullable|string', // Accepte soit URL soit chemin
                'image_file' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'suivi' => 'boolean',
                'derniere_interaction' => 'nullable|date',

                'date_conversion' => 'nullable|date',
                'campagne_id' => 'required|exists:campagnes,id',
                'type_decision' => 'nullable|string',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'assigne_a' => 'nullable|exists:users,id',
                'cellule_id' => 'nullable|exists:cellules,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Vérification date conversion
            $campagne = Campagne::find($request->campagne_id);
            if (
                $request->date_conversion &&
                ($request->date_conversion < $campagne->date_debut ||
                    ($campagne->date_fin && $request->date_conversion > $campagne->date_fin))
            ) {
                return response()->json([
                    'status' => false,
                    'message' => 'La date de conversion doit être dans la période de la campagne',
                ], 422);
            }

            // Gestion de l'image
            $data = $request->all();

            if ($request->hasFile('image_file')) {
                $path = $request->file('image_file')->store('images/ames', 'public');
                $data['image'] = $path;
            } elseif ($request->filled('image')) {
                // Si image est fourni directement (URL)
                $data['image'] = $request->image;
            }

            // Suppression des champs temporaires
            unset($data['image_file']);

            $ame = Ame::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Âme créée avec succès',
                'data' => $ame,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la création de l\'âme',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function recentes(Request $request)
    {
        try {
            // Récupère la limite facultative dans la requête, sinon 10 par défaut
            $limit = $request->get('limit', 10);

            $ames = Ame::orderBy('created_at', 'desc')
                ->take($limit)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Dernières âmes récupérées avec succès',
                'data' => $ames,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des âmes récentes',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $ame = Ame::findOrFail($id);

            return response()->json([
                'status' => true,
                'message' => 'Âme récupérée avec succès',
                'data' => $ame,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Âme non trouvée',
                'error' => $e->getMessage(),
                'data' => [],
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $ame = Ame::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255',
                'telephone' => 'nullable|string|max:20',
                'sexe' => 'required|in:H,F',
                'age' => 'nullable|integer|min:0',
                'adresse' => 'nullable|string',
                'image' => 'nullable|string',
                'image_file' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'date_conversion' => 'nullable|date',
                'campagne_id' => 'required|exists:campagnes,id',
                'type_decision' => 'nullable|string',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'assigne_a' => 'nullable|exists:users,id',
                'cellule_id' => 'nullable|exists:cellules,id',
                'suivi' => 'boolean', // 👈 ajouté
                'derniere_interaction' => 'nullable|date', // 👈 ajouté
            ]);


            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            $data = $validator->validated();

            // Gestion de l'image
            if ($request->hasFile('image_file')) {
                // Supprimer l'ancienne image si elle existe et est locale
                if ($ame->image && !filter_var($ame->image, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($ame->image);
                }
                $path = $request->file('image_file')->store('images/ames', 'public');
                $data['image'] = $path;
            } elseif ($request->filled('image_url')) {
                // Supprimer l'ancienne image si elle existe et est locale
                if ($ame->image && !filter_var($ame->image, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($ame->image);
                }
                $data['image'] = $request->image_url;
            }

            // Supprimer les champs temporaires
            unset($data['image_file']);
            unset($data['image_url']);

            $ame->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Âme mise à jour avec succès',
                'data' => $ame,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la mise à jour de l\'âme',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $ame = Ame::findOrFail($id);
            $ame->delete();

            return response()->json([
                'status' => true,
                'message' => 'Âme supprimée avec succès',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la suppression de l\'âme',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
