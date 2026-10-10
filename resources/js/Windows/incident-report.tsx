import { useSyncExternalStore } from 'react';

export type IncidentReportWindowProps = {
    attempts: number;
};

const neverChanges = () => () => {};

const timezone = () => Intl.DateTimeFormat().resolvedOptions().timeZone;
const referrer = () => document.referrer || 'none — suspicious';
const unknown = () => 'unknown';

/**
 * Everything here is read in the browser and goes nowhere.
 */
export default function IncidentReportWindow({
    attempts,
}: IncidentReportWindowProps) {
    const zone = useSyncExternalStore(neverChanges, timezone, unknown);
    const from = useSyncExternalStore(neverChanges, referrer, unknown);

    return (
        <div className="flex flex-col gap-3">
            <h3 className="text-glow">INCIDENT REPORT</h3>
            <dl className="grid grid-cols-[auto_1fr] gap-x-6 gap-y-1 text-xs">
                <dt className="text-dim">attempts</dt>
                <dd>{attempts}</dd>
                <dt className="text-dim">timezone</dt>
                <dd>{zone}</dd>
                <dt className="text-dim">referrer</dt>
                <dd className="break-all">{from}</dd>
                <dt className="text-dim">verdict</dt>
                <dd>persistent</dd>
            </dl>
            <p>Filed. It will be read never.</p>
        </div>
    );
}
