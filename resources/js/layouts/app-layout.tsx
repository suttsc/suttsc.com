import { usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import type { ReactNode } from 'react';
import Palette from '@/Shell/palette';
import ShellProvider from '@/Shell/shell-provider';
import Taskbar from '@/Shell/taskbar';

export default function AppLayout({ children }: { children: ReactNode }) {
    const { theme } = usePage().props;

    // The root element is rendered by Blade, so it only hears about a new theme from here.
    useEffect(() => {
        document.documentElement.dataset.theme = theme;
    }, [theme]);

    return (
        <ShellProvider>
            <div className="min-h-screen pb-10">{children}</div>
            <Palette />
            <Taskbar />
        </ShellProvider>
    );
}
