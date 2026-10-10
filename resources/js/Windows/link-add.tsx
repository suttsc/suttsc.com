import { useHttp } from '@inertiajs/react';
import { useId, useState } from 'react';
import type { FormEvent, ReactNode } from 'react';
import { store } from '@/routes/links';
import type { Link } from '@/types/terminal';

export type LinkAddWindowProps = {
    categories: string[];
};

type LinkForm = {
    url: string;
    author: string;
    category: string;
    reason: string;
};

function Field({
    label,
    error,
    children,
}: {
    label: string;
    error?: string;
    children: (id: string) => ReactNode;
}) {
    const id = useId();

    return (
        <div className="flex flex-col gap-1">
            <label htmlFor={id} className="text-dim text-xs">
                {label}
            </label>
            {children(id)}
            {error && <p className="text-glow text-xs">{error}</p>}
        </div>
    );
}

const inputClass =
    'border-line bg-screen focus-visible:border-ink rounded-sm border px-2 py-1 outline-none';

export default function LinkAddWindow({ categories }: LinkAddWindowProps) {
    const [shelved, setShelved] = useState<Link | null>(null);
    const http = useHttp<LinkForm, { data: Link }>({
        url: '',
        author: '',
        category: categories[0] ?? '',
        reason: '',
    });

    function submit(event: FormEvent) {
        event.preventDefault();

        http.post(store.url(), {
            onSuccess: (response) => {
                setShelved(response.data);
                http.reset();
            },
        }).catch(() => {
            // Validation errors are already on the form.
        });
    }

    return (
        <form onSubmit={submit} className="flex flex-col gap-3">
            <Field label="url" error={http.errors.url}>
                {(id) => (
                    <input
                        id={id}
                        type="url"
                        required
                        className={inputClass}
                        value={http.data.url}
                        onChange={(event) =>
                            http.setData('url', event.target.value)
                        }
                    />
                )}
            </Field>
            <Field label="author" error={http.errors.author}>
                {(id) => (
                    <input
                        id={id}
                        className={inputClass}
                        value={http.data.author}
                        onChange={(event) =>
                            http.setData('author', event.target.value)
                        }
                    />
                )}
            </Field>
            <Field label="category" error={http.errors.category}>
                {(id) => (
                    <select
                        id={id}
                        className={inputClass}
                        value={http.data.category}
                        onChange={(event) =>
                            http.setData('category', event.target.value)
                        }
                    >
                        {categories.map((category) => (
                            <option key={category} value={category}>
                                {category}
                            </option>
                        ))}
                    </select>
                )}
            </Field>
            <Field label="reason" error={http.errors.reason}>
                {(id) => (
                    <textarea
                        id={id}
                        required
                        rows={3}
                        className={inputClass}
                        value={http.data.reason}
                        onChange={(event) =>
                            http.setData('reason', event.target.value)
                        }
                    />
                )}
            </Field>
            <div className="flex items-center gap-4">
                <button
                    type="submit"
                    disabled={http.processing}
                    className="border-line hover:text-glow focus-visible:text-glow focus-visible:border-ink cursor-pointer rounded-sm border px-3 py-1 outline-none disabled:opacity-50"
                >
                    shelve
                </button>
                <p role="status" className="text-dim text-xs">
                    {shelved &&
                        `#${shelved.id} shelved. The title is on its way.`}
                </p>
            </div>
        </form>
    );
}
