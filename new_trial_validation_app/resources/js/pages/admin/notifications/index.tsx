import { Form, Head, router } from '@inertiajs/react';
import NotificationController from '@/actions/App/Http/Controllers/Admin/NotificationController';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { index as notificationsIndex } from '@/routes/admin/notifications';

type NotificationItem = {
    id: number;
    title: string;
    message: string;
    type: string;
    role_target: string | null;
    department_target: string | null;
    created_at: string;
    user: { id: number; name: string; email: string } | null;
    trial: { id: number; trial_code: string } | null;
};

type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
};

type PageProps = {
    notifications: Paginated<NotificationItem>;
};

function DeleteNotificationButton({
    notification,
}: {
    notification: NotificationItem;
}) {
    return (
        <Dialog>
            <DialogTrigger asChild>
                <Button variant="destructive" size="sm">
                    Delete
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>Delete this notification permanently?</DialogTitle>
                <DialogDescription>
                    &ldquo;{notification.title}&rdquo; will be permanently
                    removed for every user who can see it. This cannot be
                    undone.
                </DialogDescription>
                <DialogFooter className="gap-2">
                    <DialogClose asChild>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Form
                        {...NotificationController.destroy.form(
                            notification.id,
                        )}
                        options={{ preserveScroll: true }}
                    >
                        {({ processing }) => (
                            <Button
                                type="submit"
                                variant="destructive"
                                disabled={processing}
                            >
                                Delete
                            </Button>
                        )}
                    </Form>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}

export default function AdminNotificationsIndex({ notifications }: PageProps) {
    return (
        <>
            <Head title="Notifications" />

            <div className="space-y-6 p-4">
                <Heading
                    title="Admin Notifications"
                    description="Kontrol semua notification dan delete permanen jika diperlukan."
                />

                <div className="space-y-4 rounded-lg border p-4">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b text-left">
                                    <th className="p-2">ID</th>
                                    <th className="p-2">Title</th>
                                    <th className="p-2">Target</th>
                                    <th className="p-2">Trial</th>
                                    <th className="p-2">Type</th>
                                    <th className="p-2">Created At</th>
                                    <th className="p-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {notifications.data.map((notification) => (
                                    <tr
                                        key={notification.id}
                                        className="border-b align-top"
                                    >
                                        <td className="p-2">
                                            {notification.id}
                                        </td>
                                        <td className="p-2">
                                            <div>{notification.title}</div>
                                            <div className="text-muted-foreground">
                                                {notification.message}
                                            </div>
                                        </td>
                                        <td className="p-2">
                                            {notification.user?.name ??
                                                notification.role_target ??
                                                '-'}{' '}
                                            {notification.department_target ??
                                                ''}
                                        </td>
                                        <td className="p-2">
                                            {notification.trial?.trial_code ??
                                                '-'}
                                        </td>
                                        <td className="p-2">
                                            <Badge variant="outline">
                                                {notification.type}
                                            </Badge>
                                        </td>
                                        <td className="p-2">
                                            {notification.created_at}
                                        </td>
                                        <td className="p-2">
                                            <DeleteNotificationButton
                                                notification={notification}
                                            />
                                        </td>
                                    </tr>
                                ))}
                                {notifications.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={7}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada notification.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    <div className="flex items-center justify-between text-sm text-muted-foreground">
                        <span>
                            Page {notifications.current_page} of{' '}
                            {notifications.last_page} ({notifications.total}{' '}
                            notifications)
                        </span>
                        <div className="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={notifications.current_page <= 1}
                                onClick={() =>
                                    router.get(notificationsIndex().url, {
                                        page: notifications.current_page - 1,
                                    })
                                }
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={
                                    notifications.current_page >=
                                    notifications.last_page
                                }
                                onClick={() =>
                                    router.get(notificationsIndex().url, {
                                        page: notifications.current_page + 1,
                                    })
                                }
                            >
                                Next
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

AdminNotificationsIndex.layout = {
    breadcrumbs: [{ title: 'Notifications', href: notificationsIndex() }],
};
