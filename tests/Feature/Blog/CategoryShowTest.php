<?php

use App\Models\Category;

test('returns 200 and renders blog/category component', function () {
    $category = Category::factory()->create();

    $this->get(route('blog.category', $category))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('blog/category')
            ->where('name', $category->name)
            ->where('slug', $category->slug)
        );
});

test('returns 404 for non-existent category slug', function () {
    $this->get('/blog/category/non-existent-slug')
        ->assertNotFound();
});
