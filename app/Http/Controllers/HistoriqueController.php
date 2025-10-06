<?php

namespace App\Http\Controllers;

use App\Models\Historique;
use Exception;

class HistoriqueController extends Controller
{
    public function index()
    {
        try {
            $historiques = Historique::with('user')->orderBy('created_at', 'desc')->get();

            return response()->json([
                'status' => true,
                'message' => 'Historique récupéré avec succès',
                'data' => $historiques,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la récupération de l\'historique',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}