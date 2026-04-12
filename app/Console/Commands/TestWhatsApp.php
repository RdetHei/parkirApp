<?php

namespace App\Console\Commands;

use App\Models\NotificationLog;
use App\Models\User;
use App\Services\WhatsAppGateway;
use Illuminate\Console\Command;

class TestWhatsApp extends Command
{
    protected $signature = 'test:whatsapp {phone} {message=Test message from NESTON}';

    protected $description = 'Test WhatsApp notification gateway (Fonnte / UltraMsg)';

    public function handle(WhatsAppGateway $whatsapp): int
    {
        $phone = (string) $this->argument('phone');
        $message = (string) $this->argument('message');

        $this->line('Konfigurasi:');
        $this->line('  WHATSAPP_ENABLED = '.(config('services.whatsapp.enabled') ? 'true' : 'false'));
        $this->line('  WHATSAPP_DRIVER   = '.(string) config('services.whatsapp.driver', 'fonnte'));
        $this->line('  WHATSAPP_GATEWAY_URL = '.(config('services.whatsapp.url') !== '' ? '(terisi)' : '(kosong — wajib di .env)'));
        $this->line('  WHATSAPP_API_TOKEN   = '.(config('services.whatsapp.token') !== '' ? '(terisi)' : '(kosong — wajib di .env)'));
        $this->newLine();

        $this->info("Mengirim ke: {$phone}");
        $this->info('Pesan: '.$message);

        $user = new User();
        $user->phone = $phone;
        $user->name = 'Tester';

        $success = $whatsapp->sendToUser($user, $message);

        if ($success) {
            $this->info('Berhasil: pesan WhatsApp terkirim (cek juga Fonnte/UltraMsg dashboard).');

            return self::SUCCESS;
        }

        $this->error('Gagal mengirim WhatsApp.');
        $this->newLine();

        $last = NotificationLog::query()
            ->where('type', 'whatsapp')
            ->orderByDesc('id')
            ->first();

        if ($last) {
            $this->warn('Entri terakhir di notification_logs:');
            $this->line('  status: '.$last->status);
            $this->line('  detail: '.($last->message ?? '(kosong)'));
        } else {
            $this->comment('Tidak ada baris di notification_logs (gagal sebelum logging?).');
        }

        $this->newLine();
        $this->comment('Di .env setidaknya:');
        $this->comment('  WHATSAPP_ENABLED=true');
        $this->comment('  WHATSAPP_DRIVER=fonnte   # atau ultramsg');
        $this->comment('  WHATSAPP_GATEWAY_URL=... # URL API (mis. Fonnte send)');
        $this->comment('  WHATSAPP_API_TOKEN=...   # token dari penyedia');
        $this->comment('Lalu: php artisan config:clear');

        return self::FAILURE;
    }
}
