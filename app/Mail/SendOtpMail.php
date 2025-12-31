<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOtpMail extends Mailable
{
    use Queueable, SerializesModels;
    public $otp; // Variabel ini akan kita kirim ke email

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode OTP Anda', // Judul email
        );
    }

    public function content(): Content
    {
        // Kita akan membuat view 'otp.blade.php'
        return new Content(
            view: 'emails.otp',
        );
    }
}