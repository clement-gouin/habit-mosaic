<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class ReleaseMail extends Mailable
{
    use Queueable, SerializesModels;

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [strval(Config::get('mail.error.to'))],
            subject: sprintf(
                '%s [%s] : Release %s',
                ucfirst(strval(Config::get('app.name'))),
                App::environment(),
                strval(Config::get('app.version')),
            ),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.release-mail',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
