<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SMSService
{
    private const API_URL = 'https://smspilot.ru/api2.php';

    public static function sendMessage($message, $to): bool
    {
        $response = Http::acceptJson()->withHeaders([
            'Content-Type' => 'application/json',
        ])->post(self::API_URL, [
            'apikey' => config('sms.key'),

            'send' => [
                [
                    'to' => $to,
                    'text' => $message,
                ],
            ],
        ]);

        $error = $response->json('error');

        if ($error) {
            Log::error(json_encode($error));

            return false;
        }

        return true;
    }
}
