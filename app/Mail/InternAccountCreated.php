<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InternAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $recipientName,
        public readonly string $username,
        public readonly string $email,
        public readonly string $temporaryPassword,
        public readonly string $loginUrl,
        public readonly string $createdByName,
    ) {}

    public function build()
    {
        return $this
            ->subject('Akun SIMANTA Anda Sudah Dibuat')
            ->view('emails.intern-account-created');
    }
}
