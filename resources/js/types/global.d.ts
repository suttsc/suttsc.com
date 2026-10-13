import type { CommandManifestEntry, Theme } from '@/types/terminal';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            commands: CommandManifestEntry[];
            theme: Theme | null;
            ownerTimezone: string;
            [key: string]: unknown;
        };
        flashDataType: {
            /** A failed sign-in, shown to its owner once and then forgotten. */
            rejection?: { name: string | null; email: string | null };
            signedIn?: boolean;
        };
    }
}

export {};
