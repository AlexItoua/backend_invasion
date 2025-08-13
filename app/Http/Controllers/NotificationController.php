<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Notification::query();

            // Filtres
            if ($request->has('destinataire_id')) {
                $query->where('destinataire_id', $request->destinataire_id);
            }
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }
            if ($request->has('statut')) {
                $query->where('statut', $request->statut);
            }
            if ($request->has('non_lues') && $request->non_lues) {
                $query->where('statut', '!=', 'lue');
            }

            $notifications = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => true,
                'message' => 'Liste des notifications récupérée avec succès',
                'data' => $notifications,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération des notifications',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'titre' => 'required|string|max:255',
                'message' => 'required|string',
                'type' => 'required|in:sms,push,email,in_app',
                'destinataire_id' => 'nullable|exists:users,id',
                'statut' => 'sometimes|in:en_attente,envoyee,lue,echouee',
                'date_envoi' => 'nullable|date',
                'metadata' => 'nullable|json',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            $notification = Notification::create($validator->validated());

            return response()->json([
                'status' => true,
                'message' => 'Notification créée avec succès',
                'data' => $notification,
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la création de la notification',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $notification = Notification::with('destinataire')->findOrFail($id);

            // Marquer comme lue si c'est une notification in_app
            if ($notification->type === 'in_app' && $notification->statut !== 'lue') {
                $notification->marquerCommeLue();
            }

            return response()->json([
                'status' => true,
                'message' => 'Notification récupérée avec succès',
                'data' => $notification,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Notification non trouvée',
                'error' => $e->getMessage(),
                'data' => [],
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $notification = Notification::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'titre' => 'sometimes|required|string|max:255',
                'message' => 'sometimes|required|string',
                'statut' => 'sometimes|in:en_attente,envoyee,lue,echouee',
                'date_envoi' => 'sometimes|nullable|date',
                'metadata' => 'sometimes|nullable|json',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors(),
                    'data' => [],
                ], 422);
            }

            // Ne pas permettre de changer le type ou le destinataire après création
            $data = $validator->validated();
            unset($data['type'], $data['destinataire_id']);

            $notification->update($data);

            return response()->json([
                'status' => true,
                'message' => 'Notification mise à jour avec succès',
                'data' => $notification,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la mise à jour de la notification',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            $notification->delete();

            return response()->json([
                'status' => true,
                'message' => 'Notification supprimée avec succès',
                'data' => null,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la suppression de la notification',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    // Méthode supplémentaire pour marquer comme lue
    public function marquerCommeLue($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            $notification->marquerCommeLue();

            return response()->json([
                'status' => true,
                'message' => 'Notification marquée comme lue',
                'data' => $notification,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors du marquage de la notification',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
