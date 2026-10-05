<?php

use App\Http\Controllers\CommandPageController;
use App\Http\Controllers\TerminalController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');
Route::inertia('/about', 'about')->name('about');

Route::post('/terminal', TerminalController::class)->name('terminal.run');

Route::get('/{signature}', CommandPageController::class)
    ->whereIn('signature', ['help', 'uptime'])
    ->name('commands.show');
