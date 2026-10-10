import IncidentReportWindow from '@/Windows/incident-report';
import type { IncidentReportWindowProps } from '@/Windows/incident-report';
import LinkWindow from '@/Windows/link';
import type { LinkWindowProps } from '@/Windows/link';
import LinkAddWindow from '@/Windows/link-add';
import type { LinkAddWindowProps } from '@/Windows/link-add';
import StravaWindow from '@/Windows/strava';
import type { StravaWindowProps } from '@/Windows/strava';
import WhoamiWindow from '@/Windows/whoami';

type WindowBodyProps = {
    component: string;
    props: Record<string, unknown>;
};

/**
 * Maps the component name a Window DTO carries to the React component that draws it.
 */
export default function WindowBody({ component, props }: WindowBodyProps) {
    switch (component) {
        case 'link':
            return <LinkWindow {...(props as LinkWindowProps)} />;
        case 'link-add':
            return <LinkAddWindow {...(props as LinkAddWindowProps)} />;
        case 'strava':
            return <StravaWindow {...(props as StravaWindowProps)} />;
        case 'whoami':
            return <WhoamiWindow />;
        case 'incident-report':
            return (
                <IncidentReportWindow
                    {...(props as IncidentReportWindowProps)}
                />
            );
        default:
            return <p className="text-dim">This window has nothing in it.</p>;
    }
}
