import { useShell } from '@/Shell/shell-context';
import WindowFrame from '@/Shell/window-frame';
import WindowBody from '@/Windows/window-body';

export default function Desktop() {
    const { windows } = useShell();

    return windows.map((open) => (
        <WindowFrame key={open.id} window={open}>
            <WindowBody component={open.component} props={open.props} />
        </WindowFrame>
    ));
}
