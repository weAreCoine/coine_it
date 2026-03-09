<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\NavigationItem;
use App\Models\Project;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ClientsLogosService
{
    /**
     * @return array<int, array{logoUrl: string, title: string, link: NavigationItem|null}>
     */
    public static function all(): array
    {
        return cache()->remember('clientsLogos', 3600, function () {
            $projectsByLogo = Project::query()
                ->where('is_published', true)
                ->whereNotNull('client_logo')
                ->get()
                ->keyBy('client_logo');

            return collect(File::files(public_path('images/clients')))
                ->filter(fn ($file) => in_array($file->getExtension(), ['png', 'jpg', 'jpeg', 'svg', 'webp']))
                ->map(function ($file) use ($projectsByLogo) {
                    $filename = $file->getFilename();
                    $project = $projectsByLogo->get($filename);

                    return [
                        'logoUrl' => asset('images/clients/'.$filename),
                        'title' => Str::headline($file->getFilenameWithoutExtension()),
                        'link' => $project
                            ? new NavigationItem($project->title, route('projects.show', $project))
                            : null,
                    ];
                })
                ->values()
                ->all();
        });
    }
}
