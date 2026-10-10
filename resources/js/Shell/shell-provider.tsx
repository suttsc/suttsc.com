import { router, useHttp, usePage } from '@inertiajs/react';
import { useRef, useState } from 'react';
import type { ReactNode } from 'react';
import { run as terminalRun } from '@/routes/terminal';
import { ShellContext } from '@/Shell/shell-context';
import type { OpenWindow } from '@/Shell/shell-context';
import type { Output, WindowOutput } from '@/types/terminal';

const NOT_FOUND = 'Nothing. suttsc has failed you.';
const FAILED = 'Something went wrong. It has not been reported.';

export default function ShellProvider({ children }: { children: ReactNode }) {
    const path = usePage().url.split('?')[0];
    const isHome = path === '/';

    // Remembering where the palette was summoned means any navigation closes it.
    const [summonedOn, setSummonedOn] = useState<string | null>(null);
    const [output, setOutput] = useState<Output | null>(null);
    const returnFocusTo = useRef<HTMLElement | null>(null);
    const [windows, setWindows] = useState<OpenWindow[]>([]);
    const nextWindowId = useRef(1);
    const nextLayer = useRef(1);
    const http = useHttp<{ command: string }, Output>({ command: '' });

    const open = !isHome && summonedOn === path;

    function summon() {
        if (isHome || open) {
            return;
        }

        returnFocusTo.current =
            document.activeElement instanceof HTMLElement
                ? document.activeElement
                : null;
        setSummonedOn(path);
    }

    function dismiss() {
        setSummonedOn(null);
        returnFocusTo.current?.focus();
        returnFocusTo.current = null;
    }

    function show(result: Output) {
        switch (result.type) {
            case 'inline':
            case 'menu':
                setOutput(result);
                summon();
                // A command may have changed what the server shares, such as the theme.
                router.reload({ only: ['commands', 'theme'] });

                return;
            case 'navigate':
                setOutput(null);
                setSummonedOn(null);
                router.visit(result.url);

                return;
            case 'window':
                setOutput(null);
                setSummonedOn(null);
                openWindow(result);

                return;
        }
    }

    function openWindow({ title, component, props }: WindowOutput) {
        const id = nextWindowId.current++;
        const layer = nextLayer.current++;

        setWindows((current) => [
            ...current,
            { id, layer, title, component, props, minimised: false },
        ]);
    }

    function focusWindow(id: number) {
        const layer = nextLayer.current++;

        // Stacking changes through layer alone, so windows never move in the DOM and keep focus.
        setWindows((current) =>
            current.map((existing) =>
                existing.id === id
                    ? { ...existing, layer, minimised: false }
                    : existing,
            ),
        );
    }

    function closeWindow(id: number) {
        setWindows((current) =>
            current.filter((existing) => existing.id !== id),
        );
    }

    function minimiseWindow(id: number) {
        setWindows((current) =>
            current.map((existing) =>
                existing.id === id
                    ? { ...existing, minimised: true }
                    : existing,
            ),
        );
    }

    function fail(message: string) {
        setOutput({ type: 'inline', lines: [message] });
        summon();
    }

    function run(command: string) {
        http.transform(() => ({ command }));

        http.post(terminalRun.url(), {
            onSuccess: show,
            onHttpException: (response) =>
                fail(response.status === 404 ? NOT_FOUND : FAILED),
            onNetworkError: () => fail(FAILED),
        }).catch(() => {
            // Failures are already on screen; nothing further to do with the rejection.
        });
    }

    return (
        <ShellContext
            value={{
                output,
                isHome,
                open,
                busy: http.processing,
                windows,
                run,
                summon,
                dismiss,
                clear: () => setOutput(null),
                focusWindow,
                closeWindow,
                minimiseWindow,
            }}
        >
            {children}
        </ShellContext>
    );
}
