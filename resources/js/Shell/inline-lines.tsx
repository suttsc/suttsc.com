export default function InlineLines({ lines }: { lines: string[] }) {
    return (
        <div className="overflow-x-auto">
            {lines.map((line, index) => (
                <p key={index} className="whitespace-pre">
                    {line}
                </p>
            ))}
        </div>
    );
}
