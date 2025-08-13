<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;

class TestController extends Controller
{
    public function testNotification(FirebaseService $firebase)
    {
        $token = 'token_de_l_appareil'; // Token FCM récupéré depuis ton app Flutter
        $title = 'Test Notification';
        $body = 'Ceci est un test depuis Laravel';
        $data = ['key' => 'value']; // données supplémentaires optionnelles

        $response = $firebase->sendNotification($token, $title, $body, $data);

        return response()->json($response);
    }
}
