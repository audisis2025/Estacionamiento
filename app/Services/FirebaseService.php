<?php
/*
* Nombre de la clase         : FirebaseService.php
* Descripción de la clase    : Servicio para el envio de notificacion a la app de flutter
* Fecha de creación          : 20/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 20/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0 
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento : 
* Responsable                : 
* Revisor                    : 
*/
namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;

class FirebaseService
{
    private $proyectId;

    private $credentials;

    public function __construct()
    {
        $this->credentials = storage_path('app\firebase\firebase-admin.json');

        $this->proyectId = json_decode(file_get_contents($this->credentials))->project_id;
    }

    public function getAccesToken() : string
    {
        $client = new GoogleClient();
        $client-> setAuthConfig($this->credentials);
        $client-> addScope('https://www.googleapis.com/auth/firebase.messaging');

        $token = $client-> fetchAccessTokenWithAssertion();

        return $token['access_token'];
    }

    public function sendNotification(string $token, string $title, string $body, array $data) : array
    {
        $accessToken = $this->getAccesToken();

        $url = "https://fcm.googleapis.com/v1/projects/{$this->proyectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => ['title' => $title, 'body' => $body],
                'data' => $data
            ],
        ];

        $response = Http::withToken($accessToken)-> post($url, $payload);

        return $response->json();
    }
}