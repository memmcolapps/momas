<?php

namespace App\Exceptions;

use Throwable;
use App\Mail\ExceptionOccured;
use App\Models\Logger;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        // Add exceptions here that you don't want to report
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if ($this->shouldReport($e)) {
                $this->sendEmail($e);
            }
        });
    }

    /**
     * Send an email when an exception occurs.
     *
     * @param Throwable $exception
     * @return void
     */
    protected function sendEmail(Throwable $exception)
    {
        try {
            $adminEmail = config('mail.admin_email'); // Ensure this is set in your .env or config/mail.php
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new ExceptionOccured($exception));
            }
        } catch (Throwable $mailException) {
            Logger::error('Error sending exception email: ' . $mailException->getMessage());
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        // Handle HTTP 500 errors with a custom view
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $exception->getStatusCode() === 500) {
            return response()->view('errors.500', ['exception' => $exception], 500);
        }

        // Handle other exceptions
        return parent::render($request, $exception);
    }
}
