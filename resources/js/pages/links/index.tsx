import { Head, Link as InertiaLink } from '@inertiajs/react';
import { show } from '@/routes/links';
import type { Link } from '@/types/terminal';

export default function LinksIndex({ links }: { links: Link[] }) {
    return (
        <>
            <Head title="links" />
            <main className="mx-auto flex max-w-2xl flex-col gap-6 px-4 pt-16 text-sm">
                <header className="flex flex-col gap-2">
                    <h1 className="text-glow">/links</h1>
                    <p className="text-dim">
                        Things other people wrote, still being thought about.
                    </p>
                </header>
                {links.length === 0 ? (
                    <p>The shelf is empty. Reading is happening, allegedly.</p>
                ) : (
                    <ol className="flex flex-col gap-3">
                        {links.map((link) => (
                            <li key={link.id} className="flex flex-col gap-1">
                                <InertiaLink
                                    href={show(link.id)}
                                    className="text-glow outline-none hover:underline focus-visible:underline"
                                >
                                    {link.title}
                                </InertiaLink>
                                <span className="text-dim text-xs">
                                    {[link.author, link.category]
                                        .filter(Boolean)
                                        .join(' · ')}
                                </span>
                            </li>
                        ))}
                    </ol>
                )}
            </main>
        </>
    );
}
