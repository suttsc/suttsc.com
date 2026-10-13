import { Head, Link } from '@inertiajs/react';
import { index } from '@/routes/tools';
import type { ToolEntry } from '@/types/terminal';
import WindowBody from '@/Windows/window-body';

export default function ToolsShow({ tool }: { tool: ToolEntry }) {
    return (
        <>
            <Head title={tool.label}>
                <meta name="robots" content="noindex, nofollow" />
            </Head>
            <main className="mx-auto flex max-w-2xl flex-col gap-4 px-4 pt-16 text-sm">
                <header className="flex flex-col gap-1">
                    <p className="text-dim text-xs">
                        <Link
                            href={index()}
                            className="hover:text-glow focus-visible:text-glow outline-none"
                        >
                            /tools
                        </Link>
                    </p>
                    <h1 className="text-glow">{tool.label}</h1>
                    <p className="text-dim">{tool.description}</p>
                </header>
                {tool.component && (
                    <WindowBody component={tool.component} props={{}} />
                )}
            </main>
        </>
    );
}
