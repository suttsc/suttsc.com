import { usePage } from '@inertiajs/react';
import { useState } from 'react';
import { formatIn, parseMoment } from '@/lib/tools/timestamp';
import { ToolField } from '@/Windows/tools/field';

export default function TimestampTool() {
    const { ownerTimezone } = usePage().props;
    const [input, setInput] = useState('');
    const moment = parseMoment(input);
    const visitorTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

    const rows: [string, string][] = moment
        ? [
              ['unix', String(Math.floor(moment.getTime() / 1000))],
              ['unix ms', String(moment.getTime())],
              ['iso', moment.toISOString()],
              ['utc', formatIn(moment, 'UTC')],
              [`suttsc (${ownerTimezone})`, formatIn(moment, ownerTimezone)],
              [`you (${visitorTimezone})`, formatIn(moment, visitorTimezone)],
          ]
        : [];

    return (
        <div className="flex flex-col gap-3">
            <ToolField label="unix seconds, milliseconds or a date">
                {(id) => (
                    <div className="flex gap-2">
                        <input
                            id={id}
                            spellCheck={false}
                            className="border-line bg-screen focus-visible:border-ink w-full rounded-sm border px-2 py-1 outline-none"
                            value={input}
                            onChange={(event) => setInput(event.target.value)}
                        />
                        <button
                            type="button"
                            className="border-line hover:text-glow focus-visible:text-glow focus-visible:border-ink cursor-pointer rounded-sm border px-2 outline-none"
                            onClick={() =>
                                setInput(String(Math.floor(Date.now() / 1000)))
                            }
                        >
                            now
                        </button>
                    </div>
                )}
            </ToolField>
            {input.trim() !== '' && moment === null && (
                <p role="status" className="text-glow text-xs">
                    Not a moment in time anyone recognises.
                </p>
            )}
            {rows.length > 0 && (
                <dl className="grid grid-cols-[auto_1fr] gap-x-4 gap-y-1 text-xs">
                    {rows.map(([label, value]) => (
                        <div key={label} className="contents">
                            <dt className="text-dim">{label}</dt>
                            <dd className="break-all">{value}</dd>
                        </div>
                    ))}
                </dl>
            )}
        </div>
    );
}
