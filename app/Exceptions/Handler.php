<?php

namespace App\Exceptions;

use Throwable;
use RuntimeException;
use App\Mail\ErrorNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function report(Throwable $throwable): void
    {
        if ($throwable instanceof RuntimeException && $throwable->getPrevious() !== null) {
            $throwable = $throwable->getPrevious();
        }

        if ($this->shouldReport($throwable)) {
            $this->sendErrorMail($throwable);
        }

        parent::report($throwable);
    }

    protected function sendErrorMail(Throwable $throwable): void
    {
        try {
            Mail::send(new ErrorNotification($throwable));
        } catch (Throwable $mailException) {
            Log::emergency("Cannot send error notification mail", [
                'exception' => $throwable,
                'mailException' => $mailException,
            ]);
        }
    }

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
//        $this->reportable(
//            function (Throwable $e) {
//                //
//            }
//        );
    }
}
