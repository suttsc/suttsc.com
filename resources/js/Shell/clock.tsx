import { usePage } from '@inertiajs/react';
import { useSyncExternalStore } from 'react';

function everySecond(tick: () => void) {
    const timer = setInterval(tick, 1000);

    return () => clearInterval(timer);
}

const currentMinute = () => Math.floor(Date.now() / 60_000);

// The server has no business guessing the time the page will be read at.
const noMinute = () => null;

export default function Clock() {
    const { ownerTimezone } = usePage().props;
    const minute = useSyncExternalStore(everySecond, currentMinute, noMinute);

    if (minute === null) {
        return <span className="text-dim">--:--</span>;
    }

    const now = new Date(minute * 60_000);
    const time = new Intl.DateTimeFormat('en-GB', {
        timeZone: ownerTimezone,
        hour: '2-digit',
        minute: '2-digit',
    }).format(now);
    const date = new Intl.DateTimeFormat('en-GB', {
        timeZone: ownerTimezone,
        weekday: 'short',
        day: 'numeric',
        month: 'short',
    }).format(now);

    return (
        <p className="flex gap-3 whitespace-nowrap">
            <span className="text-dim max-sm:hidden">suttsc local time</span>
            <time dateTime={now.toISOString()}>
                {time} <span className="text-dim">{date}</span>
            </time>
        </p>
    );
}
