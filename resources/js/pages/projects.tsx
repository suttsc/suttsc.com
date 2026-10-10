import { Head } from '@inertiajs/react';
import type { Project } from '@/types/terminal';

export default function Projects({ projects }: { projects: Project[] }) {
    return (
        <>
            <Head title="projects" />
            <main className="mx-auto flex max-w-2xl flex-col gap-6 px-4 pt-16 text-sm">
                <header className="flex flex-col gap-2">
                    <h1 className="text-glow">/projects</h1>
                    <p className="text-dim">Things built on purpose.</p>
                </header>
                <ul className="flex flex-col gap-3">
                    {projects.map((project) => (
                        <li
                            key={project.name}
                            className="border-line bg-panel flex flex-col gap-1 rounded-md border p-4"
                        >
                            <h2 className="text-glow">
                                {project.url ? (
                                    <a
                                        href={project.url}
                                        rel="noopener noreferrer"
                                        className="outline-none hover:underline focus-visible:underline"
                                    >
                                        {project.name}
                                    </a>
                                ) : (
                                    project.name
                                )}
                            </h2>
                            <p>{project.summary}</p>
                            <p className="text-dim text-xs">{project.stack}</p>
                        </li>
                    ))}
                </ul>
            </main>
        </>
    );
}
