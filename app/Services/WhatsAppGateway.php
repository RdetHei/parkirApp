<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppGateway
{
    public function sendToUser(User $user, string $message): bool
    {
        $raw = trim((string) ($user->phone ?? ''));
        if ($raw === '') {
            $this->log($user->id, 'failed', 'Nomor WhatsApp (phone) kosong');

            return false;
        }

        $to = $this->normalizeIndonesianWa($raw);
        if ($to === '') {
            $this->log($user->id, 'failed', 'Format nomor tidak valid: '.$raw);

            return false;
        }

        if (! config('services.whatsapp.enabled', false)) {
            $this->log($user->id, 'failed', 'WhatsApp gateway dinonaktifkan (WHATSAPP_ENABLED=false)');

            return false;
        }

        $url = (string) config('services.whatsapp.url', '');
        $token = (string) config('services.whatsapp.token', '');

        if ($url === '' || $token === '') {
            $this->log($user->id, 'failed', 'WHATSAPP_GATEWAY_URL atau WHATSAPP_API_TOKEN belum diisi');

            return false;
        }

        try {
            $driver = strtolower((string) config('services.whatsapp.driver', 'fonnte'));

            if ($driver === 'ultramsg') {
                $response = Http::timeout(20)->post($url, [
                    'token' => $token,
                    'to' => $to,
                    'body' => $message,
                ]);
            } else {
                // Fonnte: header Authorization = TOKEN
                $response = Http::timeout(20)
                    ->withHeaders(['Authorization' => $token])
                    ->asForm()
                    ->post($url, [
                        'target' => $to,
                        'message' => $message,
                    ]);
            }

            if ($response->successful()) {
                $this->log($user->id, 'success', $response->body());

                return true;
            }

            $err = 'HTTP '.$response->status().': '.$response->body();
            Log::warning('WhatsAppGateway: '.$err);
            $this->log($user->id, 'failed', $err);

            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsAppGateway: '.$e->getMessage());
            $this->log($user->id, 'failed', $e->getMessage());

            return false;
        }
    }

    protected function log(?int $userId, string $status, string $message): void
    {
        NotificationLog::create([
            'user_id' => $userId,
            'type' => 'whatsapp',
            'status' => $status,
            'message' => mb_substr($message, 0, 65000),
        ]);
    }

    public function normalizeIndonesianWa(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if ($digits === '') {
            return '';
        }
        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }
        if (str_starts_with($digits, '62')) {
            return $digits;
        }
        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }
}
