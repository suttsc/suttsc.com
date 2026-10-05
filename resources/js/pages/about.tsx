import { Head } from '@inertiajs/react';

export default function About() {
    return (
        <>
            <Head title="about" />
            <main className="mx-auto flex max-w-2xl flex-col gap-4 px-4 pt-16 text-sm">
                <h1 className="text-glow">about.txt</h1>
                <p>
                    suttsc is Chris. You are also suttsc. Try not to dwell on
                    it.
                </p>
                <p>
                    He builds things for the web, mostly with Laravel, and built
                    this instead of a portfolio.
                </p>
                <p className="text-dim">Press / for everything else.</p>
            </main>
        </>
    );
}
