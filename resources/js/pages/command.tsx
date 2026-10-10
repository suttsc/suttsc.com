import { Head } from '@inertiajs/react';
import InlineLines from '@/Shell/inline-lines';
import { useShell } from '@/Shell/shell-context';
import type { InlineOutput, MenuOutput, WindowOutput } from '@/types/terminal';
import WindowBody from '@/Windows/window-body';

type CommandProps = {
    signature: string;
    output: InlineOutput | MenuOutput | WindowOutput;
};

export default function Command({ signature, output }: CommandProps) {
    const shell = useShell();

    return (
        <>
            <Head title={signature} />
            <main className="mx-auto flex max-w-2xl flex-col gap-4 px-4 pt-16 text-sm">
                <h1 className="text-glow">{signature}</h1>

                {output.type === 'inline' && (
                    <InlineLines lines={output.lines} />
                )}

                {output.type === 'window' && (
                    <WindowBody
                        component={output.component}
                        props={output.props}
                    />
                )}

                {output.type === 'menu' && (
                    <ol className="flex flex-col gap-1">
                        {output.items.map((item) => (
                            <li key={item.run} className="flex gap-3">
                                <button
                                    type="button"
                                    className="hover:text-glow focus-visible:text-glow cursor-pointer outline-none hover:underline focus-visible:underline"
                                    onClick={() => shell.run(item.run)}
                                >
                                    {item.label}
                                </button>
                                <span className="text-dim">{item.hint}</span>
                            </li>
                        ))}
                    </ol>
                )}
            </main>
        </>
    );
}
