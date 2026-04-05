<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:10',
        ]);

        try {
            // 1. Simpan ke database
            $message = Message::create($validated);

            // 2. Kirim email ke admin
            // Mengambil email admin dari .env atau default
            $adminEmail = env('MAIL_FROM_ADDRESS', 'neston2026@gmail.com');
            
            Mail::to($adminEmail)->send(new ContactMail($validated));

            return response()->json([
                'success' => true,
                'message' => 'Pesan Anda telah berhasil dikirim! Kami akan menghubungi Anda segera.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Contact Form Error: ' . $e->getMessage());
            
            // Jika database error tapi email mungkin terkirim atau sebaliknya,
            // kita beri tahu user tetap sukses jika minimal data tersimpan
            if (isset($message)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesan Anda telah diterima. Terima kasih!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Maaf, terjadi kesalahan teknis. Silakan coba lagi nanti.'
            ], 500);
        }
    }
}
