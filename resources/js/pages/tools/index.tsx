import { Head } from '@inertiajs/react';
import { useRef, useState } from 'react';
import type { KeyboardEvent } from 'react';
import { useShell } from '@/Shell/shell-context';
import type { ToolEntry } from '@/types/terminal';

const iconClass =
    'group flex w-24 cursor-pointer flex-col items-center gap-2 rounded-md p-2 text-center outline-none hover:bg-panel focus-visible:bg-panel';

function Glyph({ glyph }: { glyph: string }) {
    return (
        <span
            aria-hidden="true"
            className="border-line bg-panel text-glow group-hover:border-ink group-focus-visible:border-ink flex size-14 items-center justify-center rounded-md border text-lg"
        >
            {glyph}
        </span>
    );
}

/**
 * A desktop of icons. One click opens a tool in a window; arrow keys move between icons.
 */
export default function ToolsIndex({ tools }: { tools: ToolEntry[] }) {
    const shell = useShell();
    const [focused, setFocused] = useState(0);
    const icons = useRef<(HTMLElement | null)[]>([]);

    function open(tool: ToolEntry) {
        if (tool.component === null) {
            return;
        }

        shell.openWindow({
            title: tool.label,
            component: tool.component,
            props: {},
        });
    }

    function move(event: KeyboardEvent<HTMLUListElement>) {
        const steps: Record<string, number> = {
            ArrowRight: 1,
            ArrowDown: 1,
            ArrowLeft: -1,
            ArrowUp: -1,
        };
        let next: number | null = null;

        if (event.key in steps) {
            next =
                (focused + (steps[event.key] ?? 0) + tools.length) %
                tools.length;
        } else if (event.key === 'Home') {
            next = 0;
        } else if (event.key === 'End') {
            next = tools.length - 1;
        }

        if (next !== null) {
            event.preventDefault();
            setFocused(next);
            icons.current[next]?.focus();
        }
    }

    return (
        <>
            <Head title="tools">
                <meta name="robots" content="noindex, nofollow" />
            </Head>
            <main className="px-6 pt-8 text-sm">
                <h1 className="sr-only">Tools</h1>
                <ul
                    aria-label="Tools"
                    className="flex flex-wrap gap-4"
                    onKeyDown={move}
                >
                    {tools.map((tool, index) => {
                        const shared = {
                            ref: (element: HTMLElement | null) => {
                                icons.current[index] = element;
                            },
                            tabIndex: index === focused ? 0 : -1,
                            title: tool.description,
                            className: iconClass,
                            onFocus: () => setFocused(index),
                        };

                        return (
                            <li key={tool.slug}>
                                {tool.href ? (
                                    <a
                                        {...shared}
                                        href={tool.href}
                                        rel="noopener noreferrer"
                                    >
                                        <Glyph glyph={tool.glyph} />
                                        <span>{tool.label}</span>
                                    </a>
                                ) : (
                                    <button
                                        {...shared}
                                        type="button"
                                        onClick={() => open(tool)}
                                    >
                                        <Glyph glyph={tool.glyph} />
                                        <span>{tool.label}</span>
                                    </button>
                                )}
                            </li>
                        );
                    })}
                </ul>
                <p className="text-dim pt-8 text-xs">
                    No cookies. No ads. Nothing typed here leaves your browser.
                </p>
            </main>
        </>
    );
}
