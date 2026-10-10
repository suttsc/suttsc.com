import { Head } from '@inertiajs/react';
import type { Link } from '@/types/terminal';
import LinkWindow from '@/Windows/link';

export default function LinksShow({ link }: { link: Link }) {
    return (
        <>
            <Head title={link.title} />
            <main className="mx-auto max-w-2xl px-4 pt-16 text-sm">
                <LinkWindow link={link} />
            </main>
        </>
    );
}
