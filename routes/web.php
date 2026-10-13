<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CommandPageController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\ToolController;
use App\Http\Middleware\NoIndex;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Inertia\Inertia;

Route::inertia('/', 'welcome')->name('home');
Route::inertia('/about', 'about')->name('about');
Route::get('/hire', fn () => Inertia::render('hire', ['contactEmail' => config('site.contact_email')]))->name('hire');
Route::get('/projects', fn () => Inertia::render('projects', ['projects' => config('site.projects')]))->name('projects');

Route::post('/terminal', TerminalController::class)->name('terminal.run');

Route::get('/links', [LinkController::class, 'index'])->name('links.index');
Route::get('/links/{link}', [LinkController::class, 'show'])->whereNumber('link')->name('links.show');
Route::post('/links', [LinkController::class, 'store'])->middleware('auth')->name('links.store');

// Tools set no cookies and ask not to be indexed: no session, no CSRF token.
Route::withoutMiddleware([
    StartSession::class,
    ShareErrorsFromSession::class,
    PreventRequestForgery::class,
    AddQueuedCookiesToResponse::class,
])->middleware(NoIndex::class)->group(function () {
    Route::get('/tools', [ToolController::class, 'index'])->name('tools.index');
    Route::get('/tools/{tool}', [ToolController::class, 'show'])->name('tools.show');
});

Route::middleware('guest')->group(function () {
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

Route::get('/{signature}', CommandPageController::class)
    ->whereIn('signature', ['help', 'uptime', 'blog', 'strava', 'whoami'])
    ->name('commands.show');
