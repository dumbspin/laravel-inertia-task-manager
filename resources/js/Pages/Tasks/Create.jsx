import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import TaskForm from './Partials/TaskForm';

export default function Create({ statuses, priorities }) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Create Task
                </h2>
            }
        >
            <Head title="Create Task" />

            <div className="py-8">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="bg-white p-6 shadow-sm sm:rounded-lg">
                        <TaskForm
                            routeName="tasks.store"
                            statuses={statuses}
                            priorities={priorities}
                            submitLabel="Create Task"
                        />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
