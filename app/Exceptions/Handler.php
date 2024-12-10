<?php

namespace App\Exceptions;

use App\Traits\HelpersTraits;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use one2tek\larapi\Exceptions\ApiException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];


    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        InvalidCredentialsException::class
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Throwable $e
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof ValidationException) {
            foreach($e->errors() as $field => $errors){
                return HelpersTraits::sendError($errors[0], $e->errors(),422);
            }
        }

        if ($e instanceof AuthorizationException) {
            return response()->json([
                'success' => false,
                'message' => __('messages.authorization_error'),
            ], 403);
        }

        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'message' => __('messages.model_not_found'),
            ], 404);
        }

        if ($e instanceof HttpException) {
            if ($e->getStatusCode() == 403) {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.access_rights_not_found'),
                ], $e->getStatusCode());
            }
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getStatusCode());
        }

        // Handle other exceptions
        if ($e instanceof InvalidCredentialsException) {
            return response()->json([
                'success' => false,
                'message' => __('messages.invalid_credentials'),
            ], 401);
        }

        // Handle unaunthenticated exceptions
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'success' => false,
                'message' => trans('user::users.unauthenticated'),
            ], 401);
        }        

        // Fallback for other exceptions
        return parent::render($request, $e);
    }
}
