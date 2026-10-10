<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LinkCategory;
use Carbon\CarbonImmutable;
use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $url
 * @property ?string $title
 * @property ?string $author
 * @property LinkCategory $category
 * @property string $reason
 * @property CarbonImmutable $created_at
 * @property CarbonImmutable $updated_at
 */
final class Link extends Model
{
    /** @use HasFactory<LinkFactory> */
    use HasFactory;

    protected $fillable = ['url', 'title', 'author', 'category', 'reason'];

    /**
     * The title, or the address until the queued fetch has found one.
     */
    public function heading(): string
    {
        return $this->title ?? $this->url;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => LinkCategory::class,
        ];
    }
}
