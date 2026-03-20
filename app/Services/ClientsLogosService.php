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
        $cached = cache()->remember('clientsLogos', 3600, function () {
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
                        'filename' => $filename,
                        'title' => Str::headline($file->getFilenameWithoutExtension()),
                        'projectSlug' => $project?->slug,
                        'projectTitle' => $project?->title,
                    ];
                })
                ->values()
                ->all();
        });

        return array_map(fn (array $item) => [
            'logoUrl' => asset('images/clients/'.$item['filename']),
            'title' => $item['title'],
            'link' => $item['projectSlug']
                ? new NavigationItem($item['projectTitle'], route('projects.show', $item['projectSlug']))
                : null,
        ], $cached);
    }
}
