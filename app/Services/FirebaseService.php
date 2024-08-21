<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    public static function sendNotification(array $tokens, array $messages, $url)
    {
        $projectId = config('services.firebase.project_id');
        $apiKey = config('services.firebase.api_key');


        $client = new GoogleClient();
        $client->setAuthConfig(public_path('json/firebase.json'));

        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
      $accessTokenResponse = $client->getAccessToken();



if (!$accessTokenResponse || !isset($accessTokenResponse['access_token'])) {
    Log::error('Failed to retrieve access token', ['response' => $accessTokenResponse]);
    return response()->json(['message' => 'Failed to retrieve access token'], 500);
}

$access_token = $accessTokenResponse['access_token'];


        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];

        foreach ($tokens as $token) {
            $data = [
                "message" => [
                    "token" => $token,
                    "notification" => [
                        "title" => 'اشعار جديد من منصة تاّزر',
                        "body" => $messages['ar']['title'],
                    ],
                    "data" => [
                        "url" => $url,
                        "extra_data" => json_encode($messages)
                    ],
                ]
            ];

            self::send(json_encode($data), $headers);
        }
    }

    private static function send($data, $headers)
    {
        $projectId = config('services.firebase.project_id');
        $apiKey = config('services.firebase.api_key');

        $url = 'https://fcm.googleapis.com/v1/projects/tazzur-7cda6/messages:send';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            Log::error('Notification send error', ['error' => $err]);
            return response()->json(['message' => 'Curl Error: ' . $err], 500);
        } else {
            Log::info('Notification send response', ['response' => $response]);
            return response()->json([
                'message' => 'Notification has been sent',
                'response' => json_decode($response, true)
            ]);
        }
    }
}
