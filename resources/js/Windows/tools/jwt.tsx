import { useState } from 'react';
import { decodeJwt } from '@/lib/tools/jwt';
import { Output, textareaClass, ToolField } from '@/Windows/tools/field';

export default function JwtTool() {
    const [token, setToken] = useState('');
    const decoded = token.trim() === '' ? null : decodeJwt(token);

    return (
        <div className="flex flex-col gap-3">
            <ToolField label="token">
                {(id) => (
                    <textarea
                        id={id}
                        rows={3}
                        spellCheck={false}
                        className={textareaClass}
                        value={token}
                        onChange={(event) => setToken(event.target.value)}
                    />
                )}
            </ToolField>
            {decoded && !decoded.ok && (
                <p role="status" className="text-glow text-xs">
                    {decoded.error}
                </p>
            )}
            {decoded?.ok && (
                <>
                    <Output label="header" value={decoded.header} />
                    <Output label="payload" value={decoded.payload} />
                    {decoded.expiresAt && (
                        <p className="text-dim text-xs">
                            {decoded.expiresAt.getTime() < Date.now()
                                ? 'expired'
                                : 'expires'}{' '}
                            {decoded.expiresAt.toISOString()}
                        </p>
                    )}
                </>
            )}
            <p className="text-dim text-xs">
                Signature not verified. Decoding is not trusting.
            </p>
        </div>
    );
}
