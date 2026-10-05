<?php

declare(strict_types=1);

namespace App\Terminal;

use App\Terminal\Contracts\TerminalCommand;
use App\Terminal\Exceptions\CommandNotFound;
use App\Terminal\Output\Output;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Str;
use ReflectionClass;

final class CommandRegistry
{
    /**
     * @var array<string, TerminalCommand>|null
     */
    private ?array $commands = null;

    public function __construct(
        private readonly Container $container,
        private readonly string $path,
        private readonly string $namespace,
    ) {}

    /**
     * The commands the current visitor is allowed to see, as shipped to the client.
     *
     * @return list<array{signature: string, description: string}>
     */
    public function manifest(): array
    {
        $manifest = [];

        foreach ($this->commands() as $command) {
            if ($command->authorized() && ! $command->hidden()) {
                $manifest[] = [
                    'signature' => $command->signature(),
                    'description' => $command->description(),
                ];
            }
        }

        return $manifest;
    }

    /**
     * Run whatever was typed into the palette.
     *
     * @throws CommandNotFound
     */
    public function run(string $input): Output
    {
        $words = Str::of($input)->trim()->ltrim('/')->split('/\s+/', flags: PREG_SPLIT_NO_EMPTY)->all();

        $command = $this->match($words);

        // An unauthorised command answers exactly as a missing one would.
        if ($command === null || ! $command->authorized()) {
            throw CommandNotFound::for($input);
        }

        $arguments = array_slice($words, count(explode(' ', $command->signature())));

        $this->container->instance(Input::class, new Input($arguments));

        try {
            return $this->container->make($command::class)->handle();
        } finally {
            $this->container->forgetInstance(Input::class);
        }
    }

    /**
     * Find the command with the longest signature the typed words begin with,
     * so "/link add" wins over "/link".
     *
     * @param  array<int, string>  $words
     */
    private function match(array $words): ?TerminalCommand
    {
        $typed = Str::lower('/'.implode(' ', $words));
        $match = null;

        foreach ($this->commands() as $signature => $command) {
            $matches = $typed === $signature || Str::startsWith($typed, $signature.' ');

            if ($matches && strlen($signature) > strlen($match?->signature() ?? '')) {
                $match = $command;
            }
        }

        return $match;
    }

    /**
     * @return array<string, TerminalCommand>
     */
    private function commands(): array
    {
        return $this->commands ??= $this->discover();
    }

    /**
     * @return array<string, TerminalCommand>
     */
    private function discover(): array
    {
        $commands = [];

        foreach (glob($this->path.'/*.php') ?: [] as $file) {
            $class = $this->namespace.'\\'.basename($file, '.php');

            if (! is_subclass_of($class, TerminalCommand::class) || (new ReflectionClass($class))->isAbstract()) {
                continue;
            }

            $command = $this->container->make($class);

            $commands[$command->signature()] = $command;
        }

        ksort($commands);

        return $commands;
    }
}
