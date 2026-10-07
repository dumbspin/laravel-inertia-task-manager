import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import TaskForm from './Partials/TaskForm';

export default function Edit({ task, statuses, priorities }) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Edit Task
                </h2>
            }
        >
            <Head title="Edit Task" />

            <div className="py-8">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="bg-white p-6 shadow-sm sm:rounded-lg">
                        <TaskForm
                            task={task}
                            data={{
                                title: task.title,
                                description: task.description,
                                status: task.status,
                                priority: task.priority,
                                due_date: task.due_date,
                            }}
                            routeName="tasks.update"
                            statuses={statuses}
                            priorities={priorities}
                            submitLabel="Save Changes"
                        />
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
