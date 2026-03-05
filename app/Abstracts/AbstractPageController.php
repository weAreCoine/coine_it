<?php
declare(strict_types=1);

namespace App\Abstracts;

use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Throwable;

class AbstractPageController extends Controller
{
    protected function loadSvg(string $filename): string
    {
        try {
            return File::get(public_path('svg/'.$filename));
        } catch (Throwable $exception) {
            ExceptionHandler::handle($exception);

            return '';
        }
    }

}
