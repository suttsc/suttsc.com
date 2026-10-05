import { usePage } from '@inertiajs/react';
import { useEffect, useId, useRef, useState } from 'react';
import type { KeyboardEvent as ReactKeyboardEvent } from 'react';
import { cn } from '@/lib/utils';
import InlineLines from '@/Shell/inline-lines';
import { hasArguments, matchCommands } from '@/Shell/match-commands';
import { useShell } from '@/Shell/shell-context';
import type { MenuItem } from '@/types/terminal';

const NO_RESULTS = 'Nothing. suttsc has failed you.';

function isEditable(target: EventTarget | null): boolean {
    return (
        target instanceof HTMLElement &&
        (target.isContentEditable ||
            ['INPUT', 'TEXTAREA', 'SELECT'].includes(target.tagName))
    );
}

export default function Palette() {
    const { commands } = usePage().props;
    const shell = useShell();
    const [query, setQuery] = useState('');
    const [selected, setSelected] = useState(0);
    const inputRef = useRef<HTMLInputElement>(null);
    const listboxId = useId();

    const visible = shell.isHome || shell.open;
    const typed = query.trim();
    const menu =
        typed === '' && shell.output?.type === 'menu' ? shell.output : null;
    const inline =
        typed === '' && shell.output?.type === 'inline' ? shell.output : null;

    // One listbox serves both jobs: matching commands while typing, menu items otherwise.
    const options: MenuItem[] =
        typed !== ''
            ? matchCommands(commands, typed).map((command) => ({
                  label: command.signature,
                  hint: command.description,
                  run: command.signature,
              }))
            : (menu?.items ?? []);

    const hasOptions = options.length > 0;
    const noResults = typed !== '' && !hasOptions;
    const activeIndex = Math.min(selected, options.length - 1);
    const activeOption: MenuItem | undefined = options[activeIndex];
    const optionId = (index: number) => `${listboxId}-option-${index}`;

    useEffect(() => {
        if (visible) {
            inputRef.current?.focus();
        }
    }, [visible]);

    useEffect(() => {
        function summonFromAnywhere(event: KeyboardEvent) {
            if (
                (event.metaKey || event.ctrlKey) &&
                event.key.toLowerCase() === 'k'
            ) {
                event.preventDefault();

                if (shell.open) {
                    shell.dismiss();
                } else {
                    shell.summon();
                    inputRef.current?.focus();
                }

                return;
            }

            const unmodified =
                !event.metaKey && !event.ctrlKey && !event.altKey;

            if (event.key === '/' && unmodified && !isEditable(event.target)) {
                event.preventDefault();
                updateQuery('/');
                shell.summon();
                inputRef.current?.focus();
            }
        }

        document.addEventListener('keydown', summonFromAnywhere);

        return () =>
            document.removeEventListener('keydown', summonFromAnywhere);
    });

    function updateQuery(value: string) {
        setQuery(value);
        setSelected(0);
    }

    function execute(option: MenuItem) {
        // Keep whatever was typed after the signature, so "/theme amber" arrives whole.
        shell.run(hasArguments(typed, option.run) ? typed : option.run);
        updateQuery('');
    }

    function handleKeyDown(event: ReactKeyboardEvent<HTMLInputElement>) {
        switch (event.key) {
            case 'ArrowDown':
            case 'ArrowUp': {
                if (!hasOptions) {
                    return;
                }

                event.preventDefault();
                const step = event.key === 'ArrowDown' ? 1 : -1;
                setSelected(
                    (activeIndex + step + options.length) % options.length,
                );

                return;
            }
            case 'Enter':
                event.preventDefault();

                if (activeOption) {
                    execute(activeOption);
                }

                return;
            case 'Tab': {
                const completion =
                    typed !== '' && activeOption
                        ? `${activeOption.run} `
                        : null;

                if (completion && !query.startsWith(completion)) {
                    event.preventDefault();
                    updateQuery(completion);
                } else if (shell.open) {
                    // The input is the only stop in the dialog, so focus stays put.
                    event.preventDefault();
                }

                return;
            }
            case 'Escape':
                if (query !== '') {
                    updateQuery('');
                } else if (shell.output !== null) {
                    shell.clear();
                } else {
                    shell.dismiss();
                }

                return;
            default: {
                const pick =
                    menu && /^[1-9]$/.test(event.key)
                        ? menu.items[Number(event.key) - 1]
                        : undefined;

                if (pick) {
                    event.preventDefault();
                    execute(pick);
                }
            }
        }
    }

    if (!visible) {
        return null;
    }

    return (
        <div
            className={cn(
                'fixed inset-x-0 top-0 z-40 flex justify-center px-4 pt-[22vh]',
                shell.open && 'bg-screen/85 bottom-0',
            )}
            onMouseDown={(event) => {
                if (shell.open && event.target === event.currentTarget) {
                    shell.dismiss();
                }
            }}
        >
            <div
                className="h-fit w-full max-w-xl text-sm"
                {...(shell.open && {
                    role: 'dialog',
                    'aria-modal': true,
                    'aria-label': 'Command palette',
                })}
            >
                {shell.isHome && (
                    <h1 className="text-dim mb-4">Welcome back, suttsc.</h1>
                )}

                <div className="border-line bg-panel rounded-md border shadow-lg shadow-black/40">
                    <div className="flex items-center gap-3 px-4 py-3">
                        <span aria-hidden="true" className="text-dim">
                            ›
                        </span>
                        <input
                            ref={inputRef}
                            role="combobox"
                            aria-label="Command"
                            aria-expanded={hasOptions}
                            aria-controls={listboxId}
                            aria-autocomplete="list"
                            aria-activedescendant={
                                hasOptions ? optionId(activeIndex) : undefined
                            }
                            aria-busy={shell.busy}
                            autoComplete="off"
                            autoCapitalize="off"
                            spellCheck={false}
                            placeholder="Type / to begin."
                            className="text-glow caret-ink placeholder:text-dim w-full bg-transparent outline-none"
                            value={query}
                            onChange={(event) =>
                                updateQuery(event.target.value)
                            }
                            onKeyDown={handleKeyDown}
                        />
                    </div>

                    <ul
                        role="listbox"
                        id={listboxId}
                        aria-label={menu?.label ?? 'Commands'}
                        hidden={!hasOptions}
                        className="border-line max-h-[45vh] overflow-y-auto border-t py-1"
                    >
                        {options.map((option, index) => (
                            <li
                                key={option.run}
                                id={optionId(index)}
                                role="option"
                                aria-selected={index === activeIndex}
                                className="aria-selected:bg-line aria-selected:text-glow flex cursor-default gap-3 px-4 py-1.5"
                                // Keeps focus in the input so aria-activedescendant stays valid.
                                onMouseDown={(event) => event.preventDefault()}
                                onMouseMove={() => setSelected(index)}
                                onClick={() => execute(option)}
                            >
                                {menu && (
                                    <span
                                        aria-hidden="true"
                                        className="text-dim"
                                    >
                                        {index + 1}
                                    </span>
                                )}
                                <span>{option.label}</span>
                                <span className="text-dim ml-auto truncate">
                                    {option.hint}
                                </span>
                            </li>
                        ))}
                    </ul>

                    <div
                        role="status"
                        className="border-line border-t px-4 py-3 empty:hidden"
                    >
                        {noResults && <p>{NO_RESULTS}</p>}
                        {inline && <InlineLines lines={inline.lines} />}
                    </div>
                </div>
            </div>
        </div>
    );
}
