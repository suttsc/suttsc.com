<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SiteStatKey;
use Database\Factories\SiteStatFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property SiteStatKey $key
 * @property int $value
 */
final class SiteStat extends Model
{
    /** @use HasFactory<SiteStatFactory> */
    use HasFactory;

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = 'key';

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'key' => SiteStatKey::class,
            'value' => 'integer',
        ];
    }
}
