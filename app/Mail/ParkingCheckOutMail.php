<?php

namespace App\Mail;

use App\Models\Transaksi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ParkingCheckOutMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Transaksi $transaksi) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Check-out Parkir — '.$this->platNomor(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.parking-check-out',
        );
    }

    public function platNomor(): string
    {
        return (string) ($this->transaksi->kendaraan?->plat_nomor ?? '-');
    }
}
