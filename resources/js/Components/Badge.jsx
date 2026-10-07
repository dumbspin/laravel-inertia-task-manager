export const statusStyles = {
    pending: 'bg-amber-100 text-amber-800 ring-amber-200',
    in_progress: 'bg-blue-100 text-blue-800 ring-blue-200',
    completed: 'bg-green-100 text-green-800 ring-green-200',
};

export const priorityStyles = {
    low: 'bg-gray-100 text-gray-700 ring-gray-200',
    medium: 'bg-orange-100 text-orange-800 ring-orange-200',
    high: 'bg-red-100 text-red-800 ring-red-200',
};

export default function Badge({ value, styles, children }) {
    return (
        <span
            className={
                'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset ' +
                (styles[value] ?? 'bg-gray-100 text-gray-700 ring-gray-200')
            }
        >
            {children ?? value}
        </span>
    );
}
