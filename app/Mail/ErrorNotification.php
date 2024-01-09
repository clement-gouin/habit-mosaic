<?php

namespace App\Mail;

use Throwable;
use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\App;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Support\Facades\Request;
use Illuminate\Mail\Mailables\Envelope;

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
            to: [config('mail.error.to')],
            subject: sprintf(
                "%s [%s] : %s",
                ucfirst(config('app.name')),
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
