<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateLink;
use App\Enums\LinkCategory;
use App\Http\Requests\StoreLinkRequest;
use App\Http\Resources\LinkResource;
use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

final class LinkController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('links/index', [
            'links' => LinkResource::collection(Link::query()->latest()->latest('id')->get())->resolve(),
        ]);
    }

    public function show(Link $link): Response
    {
        return Inertia::render('links/show', [
            'link' => (new LinkResource($link))->resolve(),
        ]);
    }

    /**
     * Shelve a link from the /link add window. Owner only.
     */
    public function store(StoreLinkRequest $request, CreateLink $createLink): JsonResponse
    {
        $link = $createLink->handle(
            $request->string('url')->value(),
            $request->enum('category', LinkCategory::class) ?? LinkCategory::Other,
            $request->string('reason')->value(),
            $request->filled('author') ? $request->string('author')->value() : null,
        );

        return (new LinkResource($link))->response()->setStatusCode(201);
    }
}
