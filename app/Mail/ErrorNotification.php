<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;
use Throwable;

class ErrorNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected Throwable $exception;

    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [strval(Config::get('mail.error.to'))],
            subject: sprintf(
                '%s [%s] : %s',
                ucfirst(strval(Config::get('app.name'))),
                App::environment(),
                $this->exception->getMessage()
            ),
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'mail.error-notification',
            with: [
                'date' => date('d/m/Y H:i:s'),
                'exception' => get_class($this->exception),
                'inputs' => Request::all(),
                'version' => config('app.version'),
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
                'exception_message' => $this->exception->getMessage(),
                'trace' => $this->exception->getTrace(),
                'previous' => $this->exception->getPrevious(),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
