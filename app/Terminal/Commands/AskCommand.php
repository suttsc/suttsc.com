<?php

declare(strict_types=1);

namespace App\Terminal\Commands;

use App\Terminal\BaseCommand;
use App\Terminal\Input;
use App\Terminal\Output\Inline;
use App\Terminal\Output\Output;

/**
 * Where an unmatched query lands. Canned on purpose: a model call mid-request
 * would break the rule against third-party calls in the request cycle.
 */
final class AskCommand extends BaseCommand
{
    public function __construct(
        private readonly Input $input,
    ) {}

    public function signature(): string
    {
        return '/ask';
    }

    public function description(): string
    {
        return 'Ask a question';
    }

    public function hidden(): bool
    {
        return true;
    }

    public function handle(): Output
    {
        $question = implode(' ', $this->input->arguments);

        return new Inline([
            $question === '' ? 'Ask what?' : "\u{201C}{$question}\u{201D}",
            '',
            'Thinking about it. Has been for a while.',
            'In the meantime, /help knows everything this machine admits to.',
        ]);
    }
}
