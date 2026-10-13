import { useState } from 'react';
import { decode, encode } from '@/lib/tools/base64';
import { Output, textareaClass, ToolField } from '@/Windows/tools/field';

type Mode = 'encode' | 'decode';

export default function Base64Tool() {
    const [input, setInput] = useState('');
    const [mode, setMode] = useState<Mode>('encode');
    const [urlSafe, setUrlSafe] = useState(false);

    const decoded = mode === 'decode' ? decode(input) : null;
    const result =
        mode === 'encode'
            ? encode(input, urlSafe)
            : decoded?.ok
              ? decoded.value
              : '';

    return (
        <div className="flex flex-col gap-3">
            <div className="flex flex-wrap items-center gap-4 text-xs">
                <fieldset className="flex gap-3">
                    <legend className="sr-only">Direction</legend>
                    {(['encode', 'decode'] as const).map((option) => (
                        <label
                            key={option}
                            className="flex cursor-pointer items-center gap-1"
                        >
                            <input
                                type="radio"
                                name="base64-mode"
                                className="accent-ink"
                                checked={mode === option}
                                onChange={() => setMode(option)}
                            />
                            {option}
                        </label>
                    ))}
                </fieldset>
                {mode === 'encode' && (
                    <label className="flex cursor-pointer items-center gap-1">
                        <input
                            type="checkbox"
                            className="accent-ink"
                            checked={urlSafe}
                            onChange={(event) =>
                                setUrlSafe(event.target.checked)
                            }
                        />
                        url-safe
                    </label>
                )}
            </div>
            <ToolField label="input">
                {(id) => (
                    <textarea
                        id={id}
                        rows={4}
                        spellCheck={false}
                        className={textareaClass}
                        value={input}
                        onChange={(event) => setInput(event.target.value)}
                    />
                )}
            </ToolField>
            <Output label="output" value={result} />
            {decoded && !decoded.ok && input.trim() !== '' && (
                <p role="status" className="text-glow text-xs">
                    {decoded.error}
                </p>
            )}
        </div>
    );
}
