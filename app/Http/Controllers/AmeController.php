<?php

namespace App\Http\Controllers;

use App\Models\Ame;
use App\Models\Campagne;
use App\Models\Cellule;
use Exception;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AmeController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Ame::query();

            // Filtres
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
            if ($request->has('quartier')) {
                $query->where('quartier', 'like', '%' . $request->quartier . '%');
            }
            if ($request->has('ville')) {
                $query->where('ville', 'like', '%' . $request->ville . '%');
            }
            if ($request->has('suivi')) {
                $query->where('suivi', $request->suivi);
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $ames = $query->with(['campagne', 'encadreur', 'cellule'])->paginate($perPage);

            // 🔥 Transformer les chemins d'images en URLs complètes
            foreach ($ames as $ame) {
                if ($ame->image && !filter_var($ame->image, FILTER_VALIDATE_URL)) {
                    $ame->image = url('storage/' . $ame->image);
                }
            }

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
                'telephone' => 'required|string|max:20',
                'sexe' => 'required|in:homme,femme',
                'age' => 'nullable|integer|min:0',
                'adresse' => 'nullable|string',
                'quartier' => 'nullable|string|max:255',
                'ville' => 'nullable|string|max:255',
                'image' => 'nullable|string',
                'image_file' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'suivi' => 'boolean',
                'derniere_interaction' => 'nullable|date',
                'date_conversion' => 'nullable|date',
                'campagne_id' => 'required|exists:campagnes,id',
                'type_decision' => 'nullable|string',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'geoloc_accuracy' => 'nullable|numeric|min:0',
                'geoloc_timestamp' => 'nullable|date',
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

            $campagne = Campagne::find($request->campagne_id);
            if ($request->date_conversion) {
                if ($request->date_conversion < $campagne->date_debut) {
                    return response()->json([
                        'status' => false,
                        'message' => 'La date de conversion ne peut pas être avant le début de la campagne',
                    ], 422);
                }
                if ($campagne->date_fin && $request->date_conversion > $campagne->date_fin) {
                    return response()->json([
                        'status' => false,
                        'message' => 'La date de conversion ne peut pas être après la fin de la campagne',
                    ], 422);
                }
            }

            if (($request->latitude && !$request->longitude) || (!$request->latitude && $request->longitude)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Les deux coordonnées (latitude et longitude) sont requises pour la géolocalisation',
                ], 422);
            }

            $data = $request->all();

            // 🔥 GESTION INTELLIGENTE DES IMAGES
            if ($request->hasFile('image_file')) {
                // Upload depuis un fichier
                $path = $request->file('image_file')->store('images/ames', 'public');
                $data['image'] = $path;
            } elseif ($request->filled('image')) {
                // Upload depuis Base64
                $imageData = $request->image;

                // Vérifier si c'est du Base64
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                    // Extraire l'extension
                    $extension = $matches[1];

                    // Nettoyer le Base64
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                    $imageData = base64_decode($imageData);

                    if ($imageData === false) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Image Base64 invalide',
                        ], 422);
                    }

                    // Générer un nom unique
                    $filename = 'soul_' . time() . '_' . uniqid() . '.' . $extension;
                    $path = 'images/ames/' . $filename;

                    // Sauvegarder le fichier
                    Storage::disk('public')->put($path, $imageData);

                    $data['image'] = $path;
                } else {
                    // Si ce n'est pas du Base64, on garde la valeur telle quelle (URL ou chemin)
                    $data['image'] = $imageData;
                }
            }

            if ($request->latitude && $request->longitude) {
                if (!$request->geoloc_timestamp) {
                    $data['geoloc_timestamp'] = now();
                }

                if (!$request->cellule_id) {
                    $nearestCellule = $this->findNearestCellule($request->latitude, $request->longitude);
                    if ($nearestCellule) {
                        $data['cellule_id'] = $nearestCellule->id;
                    }
                }
            }

            unset($data['image_file']);

            $ame = Ame::create($data);

            // 🔥 Transformer le chemin en URL complète pour la réponse
            if ($ame->image && !filter_var($ame->image, FILTER_VALIDATE_URL)) {
                $ame->image = url('storage/' . $ame->image);
            }

            return response()->json([
                'status' => true,
                'message' => 'Âme créée avec succès',
                'data' => $ame->load(['campagne', 'encadreur', 'cellule']),
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
            $dateAujourdhui = now()->startOfDay();

            $query = Ame::query()
                ->where('created_at', '>=', $dateAujourdhui)
                ->orderBy('created_at', 'desc');

            if ($request->has('campagne_id')) {
                $query->where('campagne_id', $request->campagne_id);
            }

            if ($request->has('limit')) {
                $ames = $query->with(['campagne', 'encadreur', 'cellule'])
                    ->take($request->limit)
                    ->get();
            } else {
                $ames = $query->with(['campagne', 'encadreur', 'cellule'])->get();
            }

            // 🔥 Transformer les chemins d'images en URLs complètes
            $ames->transform(function ($ame) {
                if ($ame->image && !filter_var($ame->image, FILTER_VALIDATE_URL)) {
                    $ame->image = url('storage/' . $ame->image);
                }
                return $ame;
            });

            return response()->json([
                'status' => true,
                'message' => 'Âmes ajoutées aujourd\'hui récupérées avec succès',
                'data' => $ames,
                'count' => $ames->count(),
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

    private function findNearestCellule($latitude, $longitude, $maxDistance = 10)
    {
        return Cellule::selectRaw(
            "*,
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) *
            cos(radians(longitude) - radians(?)) +
            sin(radians(?)) * sin(radians(latitude)))) AS distance",
            [$latitude, $longitude, $latitude]
        )
            ->having('distance', '<', $maxDistance)
            ->orderBy('distance')
            ->first();
    }

    public function update(Request $request, $id)
    {
        try {
            $ame = Ame::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nom' => 'sometimes|required|string|max:255',
                'telephone' => 'nullable|string|max:20',
                'sexe' => 'sometimes|required|in:homme,femme',
                'age' => 'nullable|integer|min:0',
                'adresse' => 'nullable|string',
                'quartier' => 'nullable|string|max:255',
                'ville' => 'nullable|string|max:255',
                'image' => 'nullable|string',
                'image_file' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'date_conversion' => 'nullable|date',
                'campagne_id' => 'sometimes|required|exists:campagnes,id',
                'type_decision' => 'nullable|string',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'geoloc_accuracy' => 'nullable|numeric|min:0',
                'geoloc_timestamp' => 'nullable|date',
                'assigne_a' => 'nullable|exists:users,id',
                'cellule_id' => 'nullable|exists:cellules,id',
                'suivi' => 'boolean',
                'derniere_interaction' => 'nullable|date',
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

            if (isset($data['date_conversion']) && isset($data['campagne_id'])) {
                $campagne = Campagne::find($data['campagne_id']);
                if ($data['date_conversion'] < $campagne->date_debut) {
                    return response()->json([
                        'status' => false,
                        'message' => 'La date de conversion ne peut pas être avant le début de la campagne',
                    ], 422);
                }
                if ($campagne->date_fin && $data['date_conversion'] > $campagne->date_fin) {
                    return response()->json([
                        'status' => false,
                        'message' => 'La date de conversion ne peut pas être après la fin de la campagne',
                    ], 422);
                }
            }

            if ((isset($data['latitude']) && !isset($data['longitude'])) ||
                (!isset($data['latitude']) && isset($data['longitude']))
            ) {
                return response()->json([
                    'status' => false,
                    'message' => 'Les deux coordonnées (latitude et longitude) sont requises pour la géolocalisation',
                ], 422);
            }

            // 🔥 GESTION INTELLIGENTE DES IMAGES (UPDATE)
            if ($request->hasFile('image_file')) {
                // Supprimer l'ancienne image si elle existe
                if ($ame->image && !filter_var($ame->image, FILTER_VALIDATE_URL)) {
                    $oldPath = str_replace(url('storage/'), '', $ame->image);
                    Storage::disk('public')->delete($oldPath);
                }

                $path = $request->file('image_file')->store('images/ames', 'public');
                $data['image'] = $path;
            } elseif ($request->filled('image')) {
                $imageData = $request->image;

                // Vérifier si c'est du Base64
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                    // Supprimer l'ancienne image
                    if ($ame->image && !filter_var($ame->image, FILTER_VALIDATE_URL)) {
                        $oldPath = str_replace(url('storage/'), '', $ame->image);
                        Storage::disk('public')->delete($oldPath);
                    }

                    $extension = $matches[1];
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                    $imageData = base64_decode($imageData);

                    if ($imageData === false) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Image Base64 invalide',
                        ], 422);
                    }

                    $filename = 'soul_' . time() . '_' . uniqid() . '.' . $extension;
                    $path = 'images/ames/' . $filename;
                    Storage::disk('public')->put($path, $imageData);

                    $data['image'] = $path;
                } else {
                    $data['image'] = $imageData;
                }
            }

            if (isset($data['latitude']) && isset($data['longitude']) && !isset($data['cellule_id'])) {
                $nearestCellule = $this->findNearestCellule($data['latitude'], $data['longitude']);
                if ($nearestCellule) {
                    $data['cellule_id'] = $nearestCellule->id;
                }
                if (!isset($data['geoloc_timestamp'])) {
                    $data['geoloc_timestamp'] = now();
                }
            }

            unset($data['image_file']);

            $ame->update($data);

            // 🔥 Transformer le chemin en URL complète pour la réponse
            if ($ame->image && !filter_var($ame->image, FILTER_VALIDATE_URL)) {
                $ame->image = url('storage/' . $ame->image);
            }

            return response()->json([
                'status' => true,
                'message' => 'Âme mise à jour avec succès',
                'data' => $ame->load(['campagne', 'encadreur', 'cellule']),
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

    public function show($id)
    {
        try {
            $ame = Ame::with(['campagne', 'encadreur', 'cellule'])->find($id);

            if (!$ame) {
                return response()->json([
                    'status' => false,
                    'message' => 'Âme non trouvée',
                    'data' => null
                ], 404);
            }

            // 🔥 Transformer le chemin en URL complète
            if ($ame->image && !filter_var($ame->image, FILTER_VALIDATE_URL)) {
                $ame->image = url('storage/' . $ame->image);
            }

            return response()->json([
                'status' => true,
                'message' => 'Détails de l\'âme récupérés avec succès',
                'data' => $ame
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des détails de l\'âme',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $ame = Ame::findOrFail($id);

            if ($ame->image && !filter_var($ame->image, FILTER_VALIDATE_URL)) {
                $oldPath = str_replace(url('storage/'), '', $ame->image);
                Storage::disk('public')->delete($oldPath);
            }

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

    public function stats(Request $request)
    {
        try {
            $baseQuery = Ame::query();

            if ($request->has('campagne_id')) {
                $baseQuery->where('campagne_id', $request->campagne_id);
            }

            $stats = [
                'total' => (clone $baseQuery)->count(),
                'hommes' => (clone $baseQuery)->where('sexe', 'homme')->count(),
                'femmes' => (clone $baseQuery)->where('sexe', 'femme')->count(),
                'avec_geoloc' => (clone $baseQuery)->whereNotNull('latitude')->whereNotNull('longitude')->count(),
                'suivi' => (clone $baseQuery)->where('suivi', true)->count(),
                'par_quartier' => (clone $baseQuery)->select('quartier', DB::raw('count(*) as total'))
                    ->whereNotNull('quartier')
                    ->groupBy('quartier')
                    ->get(),
                'par_campagne' => (clone $baseQuery)->join('campagnes', 'ames.campagne_id', '=', 'campagnes.id')
                    ->select('campagnes.nom', DB::raw('count(ames.id) as total'))
                    ->groupBy('campagnes.id', 'campagnes.nom')
                    ->get(),
            ];

            return response()->json([
                'status' => true,
                'message' => 'Statistiques récupérées avec succès',
                'data' => $stats,
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

    public function repondreMessage(Request $request, Ame $ame)
    {
        $request->validate([
            'contenu' => 'required|string',
        ]);

        $conversation = Conversation::where('ame_id', $ame->id)->first();

        if (!$conversation) {
            return response()->json(['message' => 'Conversation non trouvée'], 404);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $ame->assigne_a,
            'contenu' => $request->contenu,
            'date_envoi' => now(),
        ]);

        return response()->json($message, 201);
    }

    public function conversations(Ame $ame)
    {
        $conversations = $ame->conversations()->with(['participants', 'dernierMessage'])->get();
        return response()->json($conversations);
    }

    public function getAmesEnSuivi()
    {
        try {
            $amesEnSuivi = Ame::enSuivi()
                ->with([
                    'cellule:id,nom,responsable_id',
                    'campagne:id,nom,date_debut,date_fin',
                    'zone:id,nom',
                    'encadreur:id,name,email',
                    'parcoursAmes' => function ($query) {
                        $query->where('statut', 'en_cours')
                            ->with([
                                'parcours:id,nom,description',
                                'etapesValidees:id,parcours_ame_id,etape_parcours_id,date_validation'
                            ]);
                    },
                    'interactions' => function ($query) {
                        $query->latest()->limit(5);
                    }
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            $amesEnSuivi = $amesEnSuivi->map(function ($ame) {
                // 🔥 Transformer l'image en URL
                if ($ame->image && !filter_var($ame->image, FILTER_VALIDATE_URL)) {
                    $ame->image = url('storage/' . $ame->image);
                }

                $ame->parcours_count = $ame->parcoursAmes->count();
                $ame->interactions_count = $ame->interactions->count();
                return $ame;
            });

            return response()->json([
                'status' => true,
                'message' => 'Âmes en suivi récupérées avec succès',
                'data' => $amesEnSuivi,
                'count' => $amesEnSuivi->count(),
                'meta' => [
                    'total_interactions' => $amesEnSuivi->sum('interactions_count'),
                    'total_parcours_actifs' => $amesEnSuivi->sum('parcours_count'),
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des âmes en suivi',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function getParcoursParAme($ameId)
    {
        try {
            $ame = Ame::with([
                'parcoursAmes' => function ($query) {
                    $query->with([
                        'parcours.etapes',
                        'etapesValidees.etape'
                    ]);
                }
            ])->findOrFail($ameId);

            // 🔥 Transformer l'image en URL
            if ($ame->image && !filter_var($ame->image, FILTER_VALIDATE_URL)) {
                $ame->image = url('storage/' . $ame->image);
            }

            return response()->json([
                'status' => true,
                'message' => 'Parcours de l\'âme récupérés avec succès',
                'data' => $ame->parcoursAmes,
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
}
