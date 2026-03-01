<?php

namespace App\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property string $slug
 * @property string $excerpt
 * @property string|null $cover
 * @property int $user_id
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property string|null $seo_image
 * @property bool $is_published
 * @property bool $is_featured
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
#[TypeScript]
class Article extends Model implements Arrayable
{
    use HasFactory;
    use SoftDeletes;

    public static string $disk = 'public';

    public string $excerpt {
        get {
            return Str::limit($this->content, preserveWords: true);
        }
    }

    public $timestamps = true;

    protected $guarded = ['id'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
        ];
    }
}
