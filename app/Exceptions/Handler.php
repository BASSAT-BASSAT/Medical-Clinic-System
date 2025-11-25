<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Handle validation exceptions
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed. Please check your input.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // Handle model not found exceptions (404)
        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found.',
                    'data' => null,
                ], 404);
            }
        });

        // Handle generic exceptions with JSON response
        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                // Default status code
                $statusCode = 500;
                $message = 'An error occurred. Please try again later.';

                // Try to extract status code if available
                if (method_exists($e, 'getStatusCode')) {
                    $statusCode = $e->getStatusCode();
                }

                // Override message for specific exceptions
                if ($e instanceof HttpResponseException) {
                    $statusCode = 400;
                    $message = 'Invalid request. Please check your input format.';
                }

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'error_type' => class_basename($e),
                ], $statusCode);
            }
        });
    }
}
