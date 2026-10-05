<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Enums\Theme;
use App\Terminal\BaseCommand;
use App\Terminal\Input;
use App\Terminal\Output\Inline;
use App\Terminal\Output\Menu;
use App\Terminal\Output\MenuItem;
use App\Terminal\Output\Output;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Str;

final class ThemeCommand extends BaseCommand
{
    public function __construct(
        private readonly Input $input,
        private readonly Session $session,
    ) {}

    public function signature(): string
    {
        return '/theme';
    }

    public function description(): string
    {
        return 'Change the phosphor';
    }

    public function handle(): Output
    {
        $choice = $this->input->argument(0);

        if ($choice === null) {
            return $this->menu();
        }

        $theme = Theme::tryFrom(Str::lower($choice));

        if ($theme === null) {
            return new Inline(["No phosphor by the name of {$choice}."]);
        }

        $this->session->put(Theme::SessionKey, $theme->value);

        return new Inline(["{$theme->label()}. Noted."]);
    }

    private function menu(): Menu
    {
        $current = Theme::current($this->session);

        return new Menu('Theme', array_map(
            fn (Theme $theme): MenuItem => new MenuItem(
                $theme->label(),
                $theme === $current ? 'current' : '',
                "/theme {$theme->value}",
            ),
            Theme::cases(),
        ));
    }
}
