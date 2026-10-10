export type CommandManifestEntry = {
    signature: string;
    description: string;
};

export type MenuItem = {
    label: string;
    hint: string;
    run: string;
};

export type InlineOutput = {
    type: 'inline';
    lines: string[];
};

export type MenuOutput = {
    type: 'menu';
    label: string;
    items: MenuItem[];
};

export type WindowOutput = {
    type: 'window';
    title: string;
    component: string;
    props: Record<string, unknown>;
};

export type NavigateOutput = {
    type: 'navigate';
    url: string;
};

export type Output = InlineOutput | MenuOutput | WindowOutput | NavigateOutput;

export type Theme = 'amber' | 'green' | 'blue' | 'ember';

export type Link = {
    id: number;
    url: string;
    title: string;
    author: string | null;
    category: string;
    reason: string;
    savedOn: string;
};

export type Run = {
    day: string;
    name: string;
    distance: string;
    pace: string;
    elevation: string;
};

export type Project = {
    name: string;
    summary: string;
    stack: string;
    url: string | null;
};
