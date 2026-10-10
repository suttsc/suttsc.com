import { Link } from '@inertiajs/react';
import { home } from '@/routes';
import Clock from '@/Shell/clock';
import { useShell } from '@/Shell/shell-context';

export default function Taskbar() {
    const { windows, focusWindow } = useShell();
    const minimised = windows.filter((open) => open.minimised);

    return (
        <nav
            aria-label="Taskbar"
            className="border-line bg-panel fixed inset-x-0 bottom-0 z-[999] flex h-10 items-center gap-4 border-t px-4 text-sm"
        >
            <Link
                href={home()}
                className="text-glow outline-none hover:underline focus-visible:underline"
            >
                suttsc
            </Link>
            <button
                type="button"
                aria-label="Back"
                className="text-dim hover:text-glow focus-visible:text-glow cursor-pointer outline-none"
                onClick={() => window.history.back()}
            >
                ←
            </button>
            <ul className="flex min-w-0 flex-1 justify-center gap-2">
                {minimised.map((open) => (
                    <li key={open.id} className="min-w-0">
                        <button
                            type="button"
                            className="border-line bg-screen text-dim hover:text-glow focus-visible:text-glow block max-w-44 cursor-pointer truncate rounded-sm border px-2 py-0.5 text-xs outline-none"
                            onClick={() => focusWindow(open.id)}
                        >
                            {open.title}
                        </button>
                    </li>
                ))}
            </ul>
            <div>
                <Clock />
            </div>
        </nav>
    );
}
