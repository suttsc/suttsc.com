<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Domain Registration Date
    |--------------------------------------------------------------------------
    |
    | The day suttsc.com was registered. /uptime counts the days since.
    |
    */

    'registered_at' => env('SITE_REGISTERED_AT', '2026-08-26'),

    /*
    |--------------------------------------------------------------------------
    | Owner Timezone
    |--------------------------------------------------------------------------
    |
    | The taskbar clock shows the owner's time, not the visitor's.
    |
    */

    'timezone' => 'Europe/London',

    /*
    |--------------------------------------------------------------------------
    | Contact
    |--------------------------------------------------------------------------
    |
    | Where /hire points people.
    |
    */

    'contact_email' => env('SITE_CONTACT_EMAIL'),

    /*
    |--------------------------------------------------------------------------
    | Projects
    |--------------------------------------------------------------------------
    |
    | What /projects lists. Hand-curated; there are few enough that a table
    | would be ceremony.
    |
    */

    'projects' => [
        [
            'name' => 'suttsc.com',
            'summary' => 'This. A command palette pretending to be an operating system.',
            'stack' => 'laravel · inertia · react · 2026',
            'url' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tool Links
    |--------------------------------------------------------------------------
    |
    | Desktop icons on /tools that are plain links rather than windows. Each
    | needs a label, description, glyph and url. Window tools live in the
    | App\Enums\Tool enum, since they need a React component.
    |
    */

    'tool_links' => [],

];
