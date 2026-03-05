<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectTag;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $users = User::factory(5)->create();
        $allUsers = $users->push($adminUser);

        // Create categories and tags
        $categories = Category::factory(8)->create();
        $tags = Tag::factory(15)->create();

        // Create articles with relationships
        Article::factory(30)
            ->recycle($allUsers)
            ->create()
            ->each(function (Article $article) use ($categories, $tags) {
                // Attach random categories (1-3 per article)
                $article->categories()->attach(
                    $categories->random(rand(1, 3))->pluck('id')
                );

                // Attach random tags (2-5 per article)
                $article->tags()->attach(
                    $tags->random(rand(2, 5))->pluck('id')
                );
            });

        // Create project categories and tags
        $projectCategories = ProjectCategory::factory(8)->create();
        $projectTags = ProjectTag::factory(15)->create();

        // Create projects with relationships
        Project::factory(15)
            ->recycle($allUsers)
            ->create()
            ->each(function (Project $project) use ($projectCategories, $projectTags) {
                $project->categories()->attach(
                    $projectCategories->random(rand(1, 3))->pluck('id')
                );

                $project->tags()->attach(
                    $projectTags->random(rand(2, 5))->pluck('id')
                );
            });
    }
}
