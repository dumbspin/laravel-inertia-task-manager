import { router } from '@inertiajs/react';

export default function Pagination({ meta }) {
    if (!meta || meta.links.length <= 3) {
        return null;
    }

    const goTo = (url) => {
        if (!url) {
            return;
        }

        router.get(url, {}, { preserveState: true, preserveScroll: true });
    };

    return (
        <nav className="flex flex-wrap items-center justify-between gap-4 px-4 py-3 sm:px-6">
            <div className="text-sm text-gray-500">
                Showing <span className="font-medium">{meta.from}</span> to{' '}
                <span className="font-medium">{meta.to}</span> of{' '}
                <span className="font-medium">{meta.total}</span> tasks
            </div>

            <div className="flex flex-wrap gap-1">
                {meta.links.map((link, index) => (
                    <button
                        key={index}
                        type="button"
                        disabled={!link.url}
                        onClick={() => goTo(link.url)}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                        className={
                            'relative inline-flex items-center rounded-md px-3 py-1.5 text-sm ring-1 ring-inset ' +
                            (link.active
                                ? 'bg-indigo-600 text-white ring-indigo-600'
                                : 'bg-white text-gray-700 ring-gray-300 hover:bg-gray-50') +
                            (!link.url ? ' cursor-default opacity-40' : '')
                        }
                    />
                ))}
            </div>
        </nav>
    );
}
