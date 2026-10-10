import { useEffect, useId, useRef, useState } from 'react';
import type {
    KeyboardEvent as ReactKeyboardEvent,
    PointerEvent as ReactPointerEvent,
    ReactNode,
} from 'react';
import { useShell } from '@/Shell/shell-context';
import type { OpenWindow } from '@/Shell/shell-context';

const WIDTH = 460;
const CASCADE = 26;
const FOCUSABLE =
    'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

function startingPosition(id: number) {
    const offset = (id % 6) * CASCADE;

    return {
        x: Math.max(16, window.innerWidth / 2 - WIDTH / 2) + offset,
        y: 96 + offset,
    };
}

type WindowFrameProps = {
    window: OpenWindow;
    children: ReactNode;
};

/**
 * Drag, focus, close, minimise. Nothing else until something needs it.
 */
export default function WindowFrame({
    window: frame,
    children,
}: WindowFrameProps) {
    const shell = useShell();
    const titleId = useId();
    const ref = useRef<HTMLDivElement>(null);
    const returnFocusTo = useRef<HTMLElement | null>(null);
    const drag = useRef<{ dx: number; dy: number } | null>(null);
    const [position, setPosition] = useState(() => startingPosition(frame.id));

    // Take focus on open and on restore; hand it back on close.
    useEffect(() => {
        if (frame.minimised) {
            return;
        }

        if (returnFocusTo.current === null) {
            returnFocusTo.current =
                document.activeElement instanceof HTMLElement
                    ? document.activeElement
                    : null;
        }

        ref.current?.focus();
    }, [frame.minimised]);

    useEffect(
        () => () => {
            if (returnFocusTo.current?.isConnected) {
                returnFocusTo.current.focus();
            }
        },
        [],
    );

    function trapFocus(event: ReactKeyboardEvent<HTMLDivElement>) {
        if (event.key === 'Escape') {
            event.stopPropagation();
            shell.closeWindow(frame.id);

            return;
        }

        if (event.key !== 'Tab' || ref.current === null) {
            return;
        }

        const focusable = [
            ...ref.current.querySelectorAll<HTMLElement>(FOCUSABLE),
        ];
        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (first === undefined || last === undefined) {
            event.preventDefault();

            return;
        }

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    }

    function startDrag(event: ReactPointerEvent<HTMLDivElement>) {
        if ((event.target as HTMLElement).closest('button')) {
            return;
        }

        drag.current = {
            dx: event.clientX - position.x,
            dy: event.clientY - position.y,
        };
        event.currentTarget.setPointerCapture(event.pointerId);
    }

    function moveDrag(event: ReactPointerEvent<HTMLDivElement>) {
        if (drag.current === null) {
            return;
        }

        setPosition({
            x: Math.min(
                Math.max(0, event.clientX - drag.current.dx),
                window.innerWidth - 80,
            ),
            y: Math.min(
                Math.max(0, event.clientY - drag.current.dy),
                window.innerHeight - 80,
            ),
        });
    }

    function minimise() {
        shell.minimiseWindow(frame.id);

        if (returnFocusTo.current?.isConnected) {
            returnFocusTo.current.focus();
        }
    }

    return (
        <div
            ref={ref}
            role="dialog"
            aria-labelledby={titleId}
            tabIndex={-1}
            hidden={frame.minimised}
            style={{
                left: position.x,
                top: position.y,
                zIndex: 45 + frame.layer,
            }}
            className="border-line bg-panel motion-safe:animate-pop fixed flex max-h-[70vh] w-[min(460px,calc(100vw-2rem))] flex-col overflow-hidden rounded-md border text-sm shadow-xl shadow-black/50 outline-none max-sm:left-4!"
            onPointerDown={() => shell.focusWindow(frame.id)}
            onKeyDown={trapFocus}
        >
            <div
                className="border-line bg-screen flex h-8 shrink-0 cursor-grab touch-none items-center gap-3 border-b px-3 select-none active:cursor-grabbing"
                onPointerDown={startDrag}
                onPointerMove={moveDrag}
                onPointerUp={() => (drag.current = null)}
                onPointerCancel={() => (drag.current = null)}
            >
                <div className="flex gap-1.5">
                    <button
                        type="button"
                        aria-label={`Close ${frame.title}`}
                        className="focus-visible:ring-glow size-3 cursor-pointer rounded-full bg-[#e05f5f] outline-none focus-visible:ring-2"
                        onClick={() => shell.closeWindow(frame.id)}
                    />
                    <button
                        type="button"
                        aria-label={`Minimise ${frame.title}`}
                        className="focus-visible:ring-glow size-3 cursor-pointer rounded-full bg-[#e0a95f] outline-none focus-visible:ring-2"
                        onClick={minimise}
                    />
                </div>
                <h2 id={titleId} className="text-dim truncate text-xs">
                    {frame.title}
                </h2>
            </div>
            <div className="overflow-y-auto p-4">{children}</div>
        </div>
    );
}
