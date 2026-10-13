<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * The window tools on /tools. Each runs entirely in the browser: nothing typed
 * into one is sent anywhere. Plain links on the same desktop live in
 * config('site.tool_links') instead, since they need no code.
 */
enum Tool: string
{
    case Base64 = 'base64';
    case Jwt = 'jwt';
    case Timestamp = 'timestamp';

    public function label(): string
    {
        return match ($this) {
            self::Base64 => 'base64',
            self::Jwt => 'jwt',
            self::Timestamp => 'timestamp',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Base64 => 'Encode and decode, URL-safe if asked',
            self::Jwt => 'Read a token. Trust nothing',
            self::Timestamp => 'Unix time, in three time zones',
        };
    }

    /**
     * The glyph drawn on the desktop icon.
     */
    public function glyph(): string
    {
        return match ($this) {
            self::Base64 => '64',
            self::Jwt => '{·}',
            self::Timestamp => '⏱',
        };
    }

    /**
     * The component under resources/js/Windows that draws a window tool.
     */
    public function component(): string
    {
        return "tool-{$this->value}";
    }

    /**
     * @return array{slug: string, label: string, description: string, glyph: string, href: null, component: string, url: string}
     */
    public function toManifest(): array
    {
        return [
            'slug' => $this->value,
            'label' => $this->label(),
            'description' => $this->description(),
            'glyph' => $this->glyph(),
            'href' => null,
            'component' => $this->component(),
            'url' => route('tools.show', $this, absolute: false),
        ];
    }
}
