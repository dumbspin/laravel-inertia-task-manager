import Badge, { priorityStyles, statusStyles } from '@/Components/Badge';
import Pagination from '@/Components/Pagination';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

function StatCard({ label, value, accent }) {
    return (
        <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div className="px-4 py-5 sm:p-6">
                <dt className="text-sm font-medium text-gray-500">{label}</dt>
                <dd className={'mt-1 text-3xl font-semibold ' + accent}>
                    {value}
                </dd>
            </div>
        </div>
    );
}

export default function Index({ tasks, filters, stats, statuses, priorities }) {
    const { flash } = usePage().props;
    const [search, setSearch] = useState(filters.search);

    useEffect(() => {
        setSearch(filters.search);
    }, [filters.search]);

    const applyFilters = (patch) => {
        router.get(
            route('dashboard'),
            { ...filters, search, ...patch },
            { preserveState: true, preserveScroll: true },
        );
    };

    const submitSearch = (event) => {
        event.preventDefault();
        applyFilters({});
    };

    const clearFilters = () => {
        setSearch('');
        router.get(
            route('dashboard'),
            {},
            { preserveState: true, preserveScroll: true },
        );
    };

    const toggleStatus = (task) => {
        router.patch(route('tasks.toggle', task.id), {}, {
            preserveScroll: true,
        });
    };

    const deleteTask = (task) => {
        if (confirm(`Delete "${task.title}"? This cannot be undone.`)) {
            router.delete(route('tasks.destroy', task.id), {
                preserveScroll: true,
            });
        }
    };

    const hasFilters =
        filters.search !== '' || filters.status !== '' || filters.priority !== '';

    return (
        <AuthenticatedLayout
            header={
                <div className="flex flex-wrap items-center justify-between gap-3">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        Task Dashboard
                    </h2>
                    <Link
                        href={route('tasks.create')}
                        className="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    >
                        + New Task
                    </Link>
                </div>
            }
        >
            <Head title="Task Dashboard" />

            <div className="py-8">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    {flash?.success && (
                        <div className="rounded-md bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-inset ring-green-200">
                            {flash.success}
                        </div>
                    )}

                    <dl className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <StatCard
                            label="Total Tasks"
                            value={stats.total}
                            accent="text-gray-900"
                        />
                        <StatCard
                            label="Pending Tasks"
                            value={stats.pending}
                            accent="text-amber-600"
                        />
                        <StatCard
                            label="In Progress Tasks"
                            value={stats.in_progress}
                            accent="text-blue-600"
                        />
                        <StatCard
                            label="Completed Tasks"
                            value={stats.completed}
                            accent="text-green-600"
                        />
                    </dl>

                    <div className="bg-white shadow-sm sm:rounded-lg">
                        <form
                            onSubmit={submitSearch}
                            className="flex flex-col gap-3 border-b border-gray-100 p-4 sm:flex-row sm:items-end"
                        >
                            <div className="flex-1">
                                <label
                                    htmlFor="search"
                                    className="block text-sm font-medium text-gray-700"
                                >
                                    Search by title
                                </label>
                                <input
                                    id="search"
                                    type="search"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    placeholder="e.g. Prepare report"
                                    className="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>

                            <div>
                                <label
                                    htmlFor="status"
                                    className="block text-sm font-medium text-gray-700"
                                >
                                    Status
                                </label>
                                <select
                                    id="status"
                                    value={filters.status}
                                    onChange={(e) =>
                                        applyFilters({ status: e.target.value })
                                    }
                                    className="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All statuses</option>
                                    {Object.entries(statuses).map(([value, label]) => (
                                        <option key={value} value={value}>
                                            {label}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            <div>
                                <label
                                    htmlFor="priority"
                                    className="block text-sm font-medium text-gray-700"
                                >
                                    Priority
                                </label>
                                <select
                                    id="priority"
                                    value={filters.priority}
                                    onChange={(e) =>
                                        applyFilters({
                                            priority: e.target.value,
                                        })
                                    }
                                    className="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All priorities</option>
                                    {Object.entries(priorities).map(
                                        ([value, label]) => (
                                            <option key={value} value={value}>
                                                {label}
                                            </option>
                                        ),
                                    )}
                                </select>
                            </div>

                            <div className="flex gap-2">
                                <button
                                    type="submit"
                                    className="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
                                >
                                    Search
                                </button>
                                {hasFilters && (
                                    <button
                                        type="button"
                                        onClick={clearFilters}
                                        className="rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                    >
                                        Clear
                                    </button>
                                )}
                            </div>
                        </form>

                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        {[
                                            'Title',
                                            'Description',
                                            'Status',
                                            'Priority',
                                            'Due Date',
                                            'Created By',
                                            'Created At',
                                            'Actions',
                                        ].map((heading) => (
                                            <th
                                                key={heading}
                                                scope="col"
                                                className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500"
                                            >
                                                {heading}
                                            </th>
                                        ))}
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200 bg-white">
                                    {tasks.data.length === 0 && (
                                        <tr>
                                            <td
                                                colSpan="8"
                                                className="px-4 py-10 text-center text-sm text-gray-500"
                                            >
                                                No tasks found. Create your first
                                                task to get started.
                                            </td>
                                        </tr>
                                    )}

                                    {tasks.data.map((task) => (
                                        <tr key={task.id} className="hover:bg-gray-50">
                                            <td className="max-w-xs px-4 py-3 text-sm font-medium text-gray-900">
                                                {task.title}
                                            </td>
                                            <td className="max-w-sm px-4 py-3 text-sm text-gray-500">
                                                <span className="line-clamp-2">
                                                    {task.description ||
                                                        '—'}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3">
                                                <Badge
                                                    value={task.status}
                                                    styles={statusStyles}
                                                >
                                                    {task.status_label}
                                                </Badge>
                                            </td>
                                            <td className="px-4 py-3">
                                                <Badge
                                                    value={task.priority}
                                                    styles={priorityStyles}
                                                >
                                                    {task.priority_label}
                                                </Badge>
                                            </td>
                                            <td className="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                {task.formatted_due_date}
                                            </td>
                                            <td className="whitespace-nowrap px-4 py-3 text-sm text-gray-700">
                                                {task.created_by}
                                            </td>
                                            <td className="whitespace-nowrap px-4 py-3 text-sm text-gray-500">
                                                {task.formatted_created_at}
                                            </td>
                                            <td className="whitespace-nowrap px-4 py-3 text-sm">
                                                <div className="flex flex-wrap gap-2">
                                                    <Link
                                                        href={route(
                                                            'tasks.edit',
                                                            task.id,
                                                        )}
                                                        className="font-medium text-indigo-600 hover:text-indigo-900"
                                                    >
                                                        Edit
                                                    </Link>
                                                    <button
                                                        type="button"
                                                        onClick={() =>
                                                            toggleStatus(task)
                                                        }
                                                        className={
                                                            task.is_completed
                                                                ? 'font-medium text-amber-600 hover:text-amber-900'
                                                                : 'font-medium text-green-600 hover:text-green-900'
                                                        }
                                                    >
                                                        {task.is_completed
                                                            ? 'Reopen'
                                                            : 'Complete'}
                                                    </button>
                                                    <button
                                                        type="button"
                                                        onClick={() =>
                                                            deleteTask(task)
                                                        }
                                                        className="font-medium text-red-600 hover:text-red-900"
                                                    >
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        <Pagination meta={tasks} />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
