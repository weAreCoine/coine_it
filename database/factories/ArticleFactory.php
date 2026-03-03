<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'title' => $title,
            'content' => $this->generateRichContent(),
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 10000),
            'cover' => 'https://placehold.co/1200x630?font=roboto',
            'user_id' => \App\Models\User::factory(),
            'seo_title' => fake()->optional()->sentence(),
            'seo_description' => fake()->optional()->text(160),
            'seo_image' => 'https://placehold.co/1200x630?font=roboto',
            'is_published' => fake()->boolean(80),
            'is_featured' => fake()->boolean(20),
        ];
    }

    /**
     * Generate rich HTML content with all typographic elements.
     */
    private function generateRichContent(): string
    {
        $faker = fake();

        $intro = '<p>'.$faker->paragraph(4).'</p>';

        $h2First = '<h2>'.$faker->sentence(4).'</h2>';
        $paraAfterH2 = '<p>'.$this->richParagraph().'</p>';
        $paraExtra = '<p>'.$faker->paragraph(3).'</p>';

        $blockquote = '<blockquote><p>'.$faker->paragraph(2).'</p></blockquote>';

        $h3First = '<h3>'.$faker->sentence(3).'</h3>';
        $paraBeforeUl = '<p>'.$faker->paragraph(2).'</p>';

        $ulItems = '';
        for ($i = 0; $i < $faker->numberBetween(3, 5); $i++) {
            $ulItems .= '<li>'.$faker->sentence().'</li>';
        }
        $ul = '<ul>'.$ulItems.'</ul>';

        $h2Second = '<h2>'.$faker->sentence(4).'</h2>';
        $paraBeforeOl = '<p>'.$this->richParagraph().'</p>';

        $olItems = '';
        for ($i = 0; $i < $faker->numberBetween(3, 6); $i++) {
            $olItems .= '<li>'.$faker->sentence().'</li>';
        }
        $ol = '<ol>'.$olItems.'</ol>';

        $paraAfterOl = '<p>'.$faker->paragraph(3).'</p>';

        $hr = '<hr>';

        $h3Second = '<h3>'.$faker->sentence(3).'</h3>';
        $paraWithLinks = '<p>'.$this->richParagraph().'</p>';
        $paraClosing = '<p>'.$this->richParagraph().'</p>';

        $blockquoteSecond = '<blockquote><p>'.$faker->paragraph(2).'</p></blockquote>';

        $h4 = '<h4>'.$faker->sentence(3).'</h4>';
        $paraFinal = '<p>'.$faker->paragraph(4).'</p>';

        return $intro
            .$h2First.$paraAfterH2.$paraExtra
            .$blockquote
            .$h3First.$paraBeforeUl.$ul
            .$h2Second.$paraBeforeOl.$ol.$paraAfterOl
            .$hr
            .$h3Second.$paraWithLinks.$paraClosing
            .$blockquoteSecond
            .$h4.$paraFinal;
    }

    /**
     * Generate a paragraph with inline rich elements (bold, italic, links).
     */
    private function richParagraph(): string
    {
        $faker = fake();
        $sentences = $faker->sentences($faker->numberBetween(3, 5));

        $richSentences = array_map(function (string $sentence) use ($faker) {
            $word = $faker->word();

            return match ($faker->numberBetween(0, 3)) {
                0 => str_replace($word, '<strong>'.$word.'</strong>', $sentence),
                1 => str_replace($word, '<em>'.$word.'</em>', $sentence),
                2 => str_replace($word, '<a href="https://example.com/'.$faker->slug(2).'">'.$word.'</a>', $sentence),
                3 => str_replace($word, '<strong><em>'.$word.'</em></strong>', $sentence),
            };
        }, $sentences);

        return implode(' ', $richSentences);
    }
}
