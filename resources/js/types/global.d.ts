import type { CommandManifestEntry, Theme } from '@/types/terminal';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            commands: CommandManifestEntry[];
            theme: Theme;
            ownerTimezone: string;
            [key: string]: unknown;
        };
    }
}

export {};
