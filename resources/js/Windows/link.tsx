import { Link as InertiaLink } from '@inertiajs/react';
import { show } from '@/routes/links';
import type { Link } from '@/types/terminal';

export type LinkWindowProps = {
    link: Link;
};

export default function LinkWindow({ link }: LinkWindowProps) {
    return (
        <article className="flex flex-col gap-3">
            <h3 className="text-glow text-base">{link.title}</h3>
            <p className="text-dim text-xs">
                {[link.author, link.category, link.savedOn]
                    .filter(Boolean)
                    .join(' · ')}
            </p>
            <p>{link.reason}</p>
            <p className="flex gap-4 text-xs">
                <a
                    href={link.url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-glow outline-none hover:underline focus-visible:underline"
                >
                    open original ↗
                </a>
                <InertiaLink
                    href={show(link.id)}
                    className="text-dim hover:text-glow focus-visible:text-glow outline-none"
                >
                    #{link.id}
                </InertiaLink>
            </p>
        </article>
    );
}
