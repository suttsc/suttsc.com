import { createContext, useContext } from 'react';
import type { Output, WindowOutput } from '@/types/terminal';

export type OpenWindow = Omit<WindowOutput, 'type'> & {
    id: number;
    /** Higher layers are drawn on top. */
    layer: number;
    minimised: boolean;
};

export type Shell = {
    /** The inline or menu output currently on show in the palette. */
    output: Output | null;
    isHome: boolean;
    open: boolean;
    busy: boolean;
    /** Open windows, in the order they were opened. */
    windows: OpenWindow[];
    run: (command: string) => void;
    summon: () => void;
    dismiss: () => void;
    clear: () => void;
    focusWindow: (id: number) => void;
    closeWindow: (id: number) => void;
    minimiseWindow: (id: number) => void;
};

export const ShellContext = createContext<Shell | null>(null);

export function useShell(): Shell {
    const shell = useContext(ShellContext);

    if (shell === null) {
        throw new Error('useShell must be used inside the shell layout.');
    }

    return shell;
}
