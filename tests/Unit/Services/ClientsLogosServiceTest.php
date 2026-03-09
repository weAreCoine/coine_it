<?php

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Entities\NavigationItem;
use App\Models\Project;
use App\Services\ClientsLogosService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    Cache::flush();
});

test('returns array of logos with correct structure', function () {
    $testDir = public_path('images/clients');
    File::ensureDirectoryExists($testDir);
    File::put($testDir.'/test-client.png', 'fake');

    $logos = ClientsLogosService::all();

    expect($logos)->toBeArray()
        ->and(collect($logos)->firstWhere('title', 'Test Client'))
        ->not->toBeNull()
        ->toHaveKeys(['logoUrl', 'title', 'link']);

    File::delete($testDir.'/test-client.png');
});

test('filters by allowed image extensions', function () {
    $testDir = public_path('images/clients');
    File::ensureDirectoryExists($testDir);

    File::put($testDir.'/valid.png', 'fake');
    File::put($testDir.'/valid2.svg', 'fake');
    File::put($testDir.'/invalid.txt', 'fake');
    File::put($testDir.'/invalid.pdf', 'fake');

    $logos = ClientsLogosService::all();

    $titles = array_column($logos, 'title');
    expect($titles)->not->toContain('Invalid');

    File::delete($testDir.'/valid.png');
    File::delete($testDir.'/valid2.svg');
    File::delete($testDir.'/invalid.txt');
    File::delete($testDir.'/invalid.pdf');
});

test('maps title from filename using Str::headline', function () {
    $testDir = public_path('images/clients');
    File::ensureDirectoryExists($testDir);

    File::put($testDir.'/my-awesome-client.png', 'fake');

    $logos = ClientsLogosService::all();

    $myClientLogo = collect($logos)->firstWhere('title', 'My Awesome Client');
    expect($myClientLogo)->not->toBeNull()
        ->and($myClientLogo['link'])->toBeNull();

    File::delete($testDir.'/my-awesome-client.png');
});

test('caches results for 3600 seconds', function () {
    $testDir = public_path('images/clients');
    File::ensureDirectoryExists($testDir);
    File::put($testDir.'/cached-test.png', 'fake');

    $first = ClientsLogosService::all();

    File::delete($testDir.'/cached-test.png');

    $second = ClientsLogosService::all();

    expect($second)->toBe($first);
});

test('returns empty array when directory has no image files', function () {
    $testDir = public_path('images/clients');
    File::ensureDirectoryExists($testDir);

    $existingFiles = File::files($testDir);
    $backup = [];
    foreach ($existingFiles as $file) {
        $backup[$file->getPathname()] = File::get($file->getPathname());
        File::delete($file->getPathname());
    }

    $logos = ClientsLogosService::all();

    foreach ($backup as $path => $content) {
        File::put($path, $content);
    }

    expect($logos)->toBeArray()->toBeEmpty();
});

test('links logo to published project with matching client_logo', function () {
    $testDir = public_path('images/clients');
    File::ensureDirectoryExists($testDir);
    File::put($testDir.'/linked-client.png', 'fake');

    $project = Project::factory()->create([
        'is_published' => true,
        'client_logo' => 'linked-client.png',
    ]);

    $logos = ClientsLogosService::all();
    $linkedLogo = collect($logos)->firstWhere('title', 'Linked Client');

    expect($linkedLogo)->not->toBeNull()
        ->and($linkedLogo['link'])->toBeInstanceOf(NavigationItem::class)
        ->and($linkedLogo['link']->title)->toBe($project->title)
        ->and($linkedLogo['link']->href)->toBe(route('projects.show', $project));

    File::delete($testDir.'/linked-client.png');
});

test('does not link logo to unpublished project', function () {
    $testDir = public_path('images/clients');
    File::ensureDirectoryExists($testDir);
    File::put($testDir.'/unpublished-client.png', 'fake');

    Project::factory()->create([
        'is_published' => false,
        'client_logo' => 'unpublished-client.png',
    ]);

    $logos = ClientsLogosService::all();
    $logo = collect($logos)->firstWhere('title', 'Unpublished Client');

    expect($logo)->not->toBeNull()
        ->and($logo['link'])->toBeNull();

    File::delete($testDir.'/unpublished-client.png');
});
