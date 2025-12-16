<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class SmsNotifier
{
    public function send(?string $to, string $message): void
    {
        if (empty($to)) {
            Log::info('SMS not sent: no recipient', ['message' => $message]);
            return;
        }

        $driver = config('services.sms.driver', 'log');

        if ($driver === 'twilio' && class_exists(Client::class)) {
            $sid = config('services.sms.twilio.sid');
            $token = config('services.sms.twilio.token');
            $from = config('services.sms.twilio.from');

            if ($sid && $token && $from) {
                try {
                    $client = new Client($sid, $token);
                    $client->messages->create($to, [
                        'from' => $from,
                        'body' => $message,
                    ]);
                    Log::info('SMS sent via Twilio', ['to' => $to]);
                    return;
                } catch (\Throwable $e) {
                    Log::error('SMS Twilio failed', ['to' => $to, 'error' => $e->getMessage()]);
                }
            } else {
                Log::warning('SMS Twilio missing credentials', ['to' => $to]);
            }
        }

        // Fallback log
        Log::info('SMS dispatch (log driver)', ['to' => $to, 'message' => $message]);
    }
}
