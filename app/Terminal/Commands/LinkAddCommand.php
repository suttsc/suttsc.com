<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Enums\LinkCategory;
use App\Terminal\Output\Output;
use App\Terminal\Output\Window;
use App\Terminal\OwnerCommand;

final class LinkAddCommand extends OwnerCommand
{
    public function signature(): string
    {
        return '/link add';
    }

    public function description(): string
    {
        return 'Put a link on the shelf';
    }

    public function handle(): Output
    {
        return new Window('links — new record', 'link-add', [
            'categories' => array_column(LinkCategory::cases(), 'value'),
        ]);
    }
}
