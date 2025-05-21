<?php

declare(strict_types=1);

namespace App\Services;

use App\Casteller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificator
{
    private string $projectId;

    private string $clientEmail;

    private string $privateKey;

    private string $fcmUrl;

    private ?string $accessToken = null;

    private ?int $tokenExpiration = null;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id');
        $this->clientEmail = config('services.firebase.client_email');
        $this->privateKey = config('services.firebase.private_key');
        $this->fcmUrl = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
    }

    /**
     * Sends a Firebase notification payload to a Casteller.
     *
     * @param  Casteller  $casteller  The casteller to send the notification to
     * @param  string  $payload  The pre-formatted notification payload as JSON string
     * @return bool Returns true if there was an error, false otherwise
     */
    public function sendPayload(Casteller $casteller, string $payload): bool
    {
        if (! $this->projectId || ! $this->clientEmail || ! $this->privateKey) {
            Log::error('Firebase configuration not complete');

            return true;
        }

        $token = $casteller->getCastellerConfig()->getFirebaseToken();

        if (empty($token)) {
            Log::error('Firebase token not found for casteller: '.$casteller->getId());

            return true;
        }

        try {
            // Get access token if not set or expired
            if ($this->accessToken === null || time() > $this->tokenExpiration) {
                $this->getAccessToken();
            }

            // Decode the payload
            $payloadData = json_decode($payload, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON payload for Firebase notification', [
                    'casteller_id' => $casteller->getId(),
                    'payload' => $payload,
                    'error' => json_last_error_msg(),
                ]);

                return true;
            }

            // Add the token to the message
            $payloadData['message']['token'] = $token;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, $payloadData);

            if (! $response->successful()) {
                Log::error('Failed to send Firebase notification', [
                    'casteller_id' => $casteller->getId(),
                    'response' => $response->json(),
                    'status' => $response->status(),
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Exception when sending Firebase notification: '.$e->getMessage(), [
                'casteller_id' => $casteller->getId(),
                'exception' => $e,
            ]);

            return true;
        }
    }

    /**
     * Get an access token for Firebase Cloud Messaging API
     */
    private function getAccessToken(): void
    {
        try {
            $now = time();

            // Create JWT header
            $header = json_encode([
                'alg' => 'RS256',
                'typ' => 'JWT',
            ]);

            // Create JWT claim set
            $payload = json_encode([
                'iss' => $this->clientEmail,
                'sub' => $this->clientEmail,
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            ]);

            // Encode Header
            $base64UrlHeader = $this->base64UrlEncode($header);

            // Encode Payload
            $base64UrlPayload = $this->base64UrlEncode($payload);

            // Create Signature
            $unsignedToken = $base64UrlHeader.'.'.$base64UrlPayload;
            $privateKey = openssl_pkey_get_private($this->privateKey);
            openssl_sign($unsignedToken, $signature, $privateKey, 'SHA256');
            $base64UrlSignature = $this->base64UrlEncode($signature);

            // Create JWT
            $jwt = $base64UrlHeader.'.'.$base64UrlPayload.'.'.$base64UrlSignature;

            // Request Access Token
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            $responseData = $response->json();

            if (isset($responseData['access_token'])) {
                $this->accessToken = $responseData['access_token'];
                $this->tokenExpiration = $now + $responseData['expires_in'] - 60; // Subtract 60 seconds for safety
            } else {
                Log::error('Failed to get Firebase access token', ['response' => $responseData]);
            }
        } catch (\Exception $e) {
            Log::error('Exception when getting Firebase access token: '.$e->getMessage());
        }
    }

    /**
     * Base64Url encode a string
     */
    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
