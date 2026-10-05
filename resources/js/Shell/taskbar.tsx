import { Link } from '@inertiajs/react';
import { home } from '@/routes';
import Clock from '@/Shell/clock';

export default function Taskbar() {
    return (
        <nav
            aria-label="Taskbar"
            className="border-line bg-panel fixed inset-x-0 bottom-0 z-50 flex h-10 items-center gap-4 border-t px-4 text-sm"
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
            <div className="ml-auto">
                <Clock />
            </div>
        </nav>
    );
}
