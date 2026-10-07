import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Link, useForm } from '@inertiajs/react';

const DEFAULTS = {
    title: '',
    description: '',
    status: 'pending',
    priority: 'medium',
    due_date: '',
};

export default function TaskForm({
    data: initialData = {},
    routeName,
    task = null,
    statuses,
    priorities,
    submitLabel,
}) {
    const { data, setData, post, put, processing, errors } = useForm({
        ...DEFAULTS,
        ...initialData,
    });

    const submit = (event) => {
        event.preventDefault();

        if (task) {
            put(route(routeName, task.id));
        } else {
            post(route(routeName));
        }
    };

    const fieldClass =
        'mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500';

    return (
        <form onSubmit={submit} className="space-y-6">
            <div>
                <InputLabel htmlFor="title" value="Title" required />
                <TextInput
                    id="title"
                    name="title"
                    value={data.title}
                    className={fieldClass}
                    isFocused
                    maxLength={255}
                    onChange={(e) => setData('title', e.target.value)}
                    placeholder="e.g. Prepare monthly report"
                />
                <InputError message={errors.title} className="mt-2" />
            </div>

            <div>
                <InputLabel htmlFor="description" value="Description" />
                <textarea
                    id="description"
                    name="description"
                    rows={4}
                    value={data.description ?? ''}
                    onChange={(e) => setData('description', e.target.value)}
                    placeholder="Optional details about the task"
                    className={
                        fieldClass +
                        ' rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500'
                    }
                />
                <InputError message={errors.description} className="mt-2" />
            </div>

            <div className="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div>
                    <InputLabel htmlFor="status" value="Status" required />
                    <select
                        id="status"
                        name="status"
                        value={data.status}
                        onChange={(e) => setData('status', e.target.value)}
                        className={fieldClass}
                    >
                        {Object.entries(statuses).map(([value, label]) => (
                            <option key={value} value={value}>
                                {label}
                            </option>
                        ))}
                    </select>
                    <InputError message={errors.status} className="mt-2" />
                </div>

                <div>
                    <InputLabel htmlFor="priority" value="Priority" required />
                    <select
                        id="priority"
                        name="priority"
                        value={data.priority}
                        onChange={(e) => setData('priority', e.target.value)}
                        className={fieldClass}
                    >
                        {Object.entries(priorities).map(([value, label]) => (
                            <option key={value} value={value}>
                                {label}
                            </option>
                        ))}
                    </select>
                    <InputError message={errors.priority} className="mt-2" />
                </div>

                <div>
                    <InputLabel htmlFor="due_date" value="Due Date" required />
                    <TextInput
                        id="due_date"
                        name="due_date"
                        type="date"
                        value={data.due_date ?? ''}
                        className={fieldClass}
                        onChange={(e) => setData('due_date', e.target.value)}
                    />
                    <InputError message={errors.due_date} className="mt-2" />
                </div>
            </div>

            <div className="flex items-center justify-end gap-4">
                <Link
                    href={route('dashboard')}
                    className="text-sm text-gray-600 underline hover:text-gray-900"
                >
                    Cancel
                </Link>
                <PrimaryButton disabled={processing}>{submitLabel}</PrimaryButton>
            </div>
        </form>
    );
}
