<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Link
 */
final class LinkResource extends JsonResource
{
    /**
     * @return array{id: int, url: string, title: string, author: ?string, category: string, reason: string, savedOn: string}
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'title' => $this->heading(),
            'author' => $this->author,
            'category' => $this->category->value,
            'reason' => $this->reason,
            'savedOn' => $this->created_at->toDateString(),
        ];
    }
}
