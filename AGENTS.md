# Repository Guidelines

## Project Structure & Module Organization
This repository is a Laravel 12 application with an Inertia/React frontend. Backend code lives in `app/`, with HTTP controllers under `app/Http/Controllers`, domain models in `app/Models`, shared value objects in `app/Entities`, and Filament admin resources in `app/Filament`. Frontend pages, components, hooks, and types live in `resources/js/`. Blade entry views stay in `resources/views`, routes in `routes/`, and database factories, seeders, and migrations in `database/`. Tests are split into `tests/Feature` and `tests/Unit`.

## Build, Test, and Development Commands
Use `composer setup` for a fresh install: it installs PHP and Node dependencies, creates `.env`, generates the app key, runs migrations, and builds assets. Use `composer dev` for the standard local stack (`artisan serve`, queue listener, log tailing, and Vite). Use `npm run dev` when you only need the frontend dev server. Build production assets with `npm run build` or `npm run build:ssr`. Run backend checks with `composer test`, PHP formatting with `composer lint`, frontend lint fixes with `npm run lint`, formatting with `npm run format`, and TypeScript checks with `npm run types`.

## Coding Style & Naming Conventions
Follow `.editorconfig`: UTF-8, LF endings, and 4-space indentation for code and Markdown; YAML uses 2 spaces. PHP formatting is enforced by Laravel Pint with the `laravel` preset. Frontend code uses Prettier and ESLint; prefer single quotes, semicolons, and keep imports ordered alphabetically by group. Use PascalCase for PHP classes and React components, camelCase for methods, hooks, and helpers, and descriptive page filenames such as `resources/js/pages/projects/show.tsx`.

## Testing Guidelines
Tests run with Pest on top of PHPUnit. Put request, UI, and integration coverage in `tests/Feature`; keep isolated logic checks in `tests/Unit`. Name test files with the subject plus `Test.php` such as `tests/Feature/Projects/ProjectShowTest.php`. The test suite uses an in-memory SQLite database by default, so keep factories and migrations current when changing persistence behavior.

## Commit & Pull Request Guidelines
Recent history follows Conventional Commit style like `feat(ui): ...` and `fix(routes): ...`; keep that pattern for new work. Pull requests should include a short description, linked issue or task, notes on migrations or config changes, and screenshots for UI updates. Before opening a PR, run `composer test`, `npm run lint`, and `npm run types`.
