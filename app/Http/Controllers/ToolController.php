<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Tool;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

final class ToolController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('tools/index', [
            'tools' => [
                ...array_map(fn (Tool $tool): array => $tool->toManifest(), Tool::cases()),
                ...$this->links(),
            ],
        ]);
    }

    public function show(Tool $tool): Response
    {
        return Inertia::render('tools/show', [
            'tool' => $tool->toManifest(),
        ]);
    }

    /**
     * @return list<array{slug: string, label: string, description: string, glyph: string, href: string, component: null, url: string}>
     */
    private function links(): array
    {
        /** @var list<array{label: string, description: string, glyph: string, url: string}> $links */
        $links = config('site.tool_links', []);

        return array_map(fn (array $link): array => [
            'slug' => Str::slug($link['label']),
            'label' => $link['label'],
            'description' => $link['description'],
            'glyph' => $link['glyph'],
            'href' => $link['url'],
            'component' => null,
            'url' => $link['url'],
        ], $links);
    }
}
