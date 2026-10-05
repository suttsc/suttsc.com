import { createContext, useContext } from 'react';
import type { Output } from '@/types/terminal';

export type Shell = {
    /** The inline or menu output currently on show in the palette. */
    output: Output | null;
    isHome: boolean;
    open: boolean;
    busy: boolean;
    run: (command: string) => void;
    summon: () => void;
    dismiss: () => void;
    clear: () => void;
};

export const ShellContext = createContext<Shell | null>(null);

export function useShell(): Shell {
    const shell = useContext(ShellContext);

    if (shell === null) {
        throw new Error('useShell must be used inside the shell layout.');
    }

    return shell;
}
