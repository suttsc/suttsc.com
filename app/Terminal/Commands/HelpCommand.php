<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Terminal\BaseCommand;
use App\Terminal\CommandRegistry;
use App\Terminal\Output\Menu;
use App\Terminal\Output\MenuItem;
use App\Terminal\Output\Output;

final class HelpCommand extends BaseCommand
{
    public function __construct(
        private readonly CommandRegistry $registry,
    ) {}

    public function signature(): string
    {
        return '/help';
    }

    public function description(): string
    {
        return 'Everything this machine admits to';
    }

    public function handle(): Output
    {
        return new Menu('Commands', array_map(
            fn (array $command): MenuItem => new MenuItem($command['signature'], $command['description'], $command['signature']),
            $this->registry->manifest(),
        ));
    }
}
