<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $plainPassword,
        public string $loginUrl
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your '.config('app.name', 'OpenProject').' Account Has Been Created',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-created',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
