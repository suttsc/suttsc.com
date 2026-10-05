<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RunCommandRequest;
use App\Terminal\CommandRegistry;
use Illuminate\Http\JsonResponse;

final class TerminalController extends Controller
{
    /**
     * Run a command typed into the palette.
     */
    public function __invoke(RunCommandRequest $request, CommandRegistry $registry): JsonResponse
    {
        return response()->json($registry->run($request->string('command')->value()));
    }
}
