import { useId } from 'react';
import type { ReactNode } from 'react';

export const textareaClass =
    'border-line bg-screen focus-visible:border-ink w-full rounded-sm border px-2 py-1 font-mono outline-none';

export function ToolField({
    label,
    children,
}: {
    label: string;
    children: (id: string) => ReactNode;
}) {
    const id = useId();

    return (
        <div className="flex flex-col gap-1">
            <label htmlFor={id} className="text-dim text-xs">
                {label}
            </label>
            {children(id)}
        </div>
    );
}

export function Output({ label, value }: { label: string; value: string }) {
    return (
        <div className="flex flex-col gap-1">
            <p className="text-dim text-xs">{label}</p>
            <output className="border-line bg-screen block max-h-48 overflow-auto rounded-sm border px-2 py-1 break-all whitespace-pre-wrap">
                {value || ' '}
            </output>
        </div>
    );
}
