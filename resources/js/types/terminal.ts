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
