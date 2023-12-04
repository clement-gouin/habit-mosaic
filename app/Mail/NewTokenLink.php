<?php

namespace App\Mail;

use App\Models\UserToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewTokenLink extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected UserToken $token, protected bool $newUser)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome ' . ($this->newUser ? 'back ' : '') . 'to ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.new-token-link',
            with: [
                'newUser' => $this->newUser,
                'user' => $this->token->user->name,
                'url' => config('app.url') . '/login/' . $this->token->token
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
