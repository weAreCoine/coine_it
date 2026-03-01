<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Support\Facades\Log;
use Throwable;

class ExceptionHandler
{
    public static function handle(Throwable $exception): void
    {
        Log::error($exception->getMessage(), [
            'exception' => $exception::class,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace()
        ]);
    }
}
