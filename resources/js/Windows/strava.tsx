import type { Run } from '@/types/terminal';

export type StravaWindowProps = {
    runs: Run[];
    total: string;
    syncedAgo: string | null;
};

export default function StravaWindow({
    runs,
    total,
    syncedAgo,
}: StravaWindowProps) {
    if (runs.length === 0) {
        return (
            <p>
                No runs this week. Resting, strategically.
                {syncedAgo === null && (
                    <span className="text-dim block pt-2 text-xs">
                        Strava has never been asked.
                    </span>
                )}
            </p>
        );
    }

    return (
        <div className="flex flex-col gap-3">
            <table className="w-full text-xs tabular-nums">
                <thead>
                    <tr className="text-dim border-line border-b text-left uppercase">
                        <th className="pb-2 font-normal">day</th>
                        <th className="pb-2 text-right font-normal">dist</th>
                        <th className="pb-2 text-right font-normal">pace</th>
                        <th className="pb-2 text-right font-normal">elev</th>
                    </tr>
                </thead>
                <tbody>
                    {runs.map((run, index) => (
                        <tr
                            key={index}
                            className="border-line border-b last:border-0"
                            title={run.name}
                        >
                            <td className="py-2">{run.day}</td>
                            <td className="py-2 text-right">{run.distance}</td>
                            <td className="py-2 text-right">{run.pace}</td>
                            <td className="py-2 text-right">{run.elevation}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
            <p className="text-dim text-xs">
                {total} this week
                {syncedAgo !== null && ` · synced ${syncedAgo}`}
            </p>
        </div>
    );
}
