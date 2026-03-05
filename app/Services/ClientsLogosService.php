<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ClientsLogosService
{
    public static function all(): array
    {
        return cache()->remember('clientsLogos', 3600, function () {
            return collect(File::files(public_path('images/clients')))
                ->filter(fn($file) => in_array($file->getExtension(), ['png', 'jpg', 'jpeg', 'svg', 'webp']))
                ->map(fn($file) => [
                    'logoUrl' => asset('images/clients/'.$file->getFilename()),
                    'title' => Str::headline($file->getFilenameWithoutExtension()),
                    'link' => null,
                ])
                ->values()
                ->all();
        });
    }
}
