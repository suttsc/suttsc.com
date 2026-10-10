import { Head } from '@inertiajs/react';

export default function Hire({
    contactEmail,
}: {
    contactEmail: string | null;
}) {
    return (
        <>
            <Head title="hire" />
            <main className="mx-auto flex max-w-2xl flex-col gap-6 px-4 pt-16 text-sm">
                <header className="flex flex-col gap-2">
                    <h1 className="text-glow">Availability: negotiable</h1>
                    <p className="text-dim">
                        Enthusiasm: high. Notice period: standard. Opinions
                        about service layers: several.
                    </p>
                </header>
                <section className="border-line bg-panel flex flex-col gap-2 rounded-md border p-4">
                    <h2 className="text-glow">What I'm interested in</h2>
                    <p>
                        Teams where architecture is a shared conversation rather
                        than something one person defends. Laravel shops that
                        have outgrown the tutorial patterns and know it.
                        Anything where I get to keep leading people as well as
                        writing code.
                    </p>
                    {contactEmail && (
                        <p className="text-xs">
                            <a
                                href={`mailto:${contactEmail}`}
                                className="text-glow outline-none hover:underline focus-visible:underline"
                            >
                                {contactEmail}
                            </a>
                        </p>
                    )}
                </section>
            </main>
        </>
    );
}
