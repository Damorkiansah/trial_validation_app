import { Head } from '@inertiajs/react';
import NotificationController from '@/actions/App/Http/Controllers/Admin/NotificationController';
import { ConfirmDialog } from '@/components/confirm-dialog';
import Heading from '@/components/heading';
import { PaginationFooter } from '@/components/pagination-footer';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { index as notificationsIndex } from '@/routes/admin/notifications';
import type { Paginated } from '@/types';

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

type PageProps = {
    notifications: Paginated<NotificationItem>;
};

export default function AdminNotificationsIndex({ notifications }: PageProps) {
    return (
        <>
            <Head title="Notifications" />

            <div className="space-y-6 p-4">
                <Heading
                    title="Admin Notifications"
                    description="Kontrol semua notification dan delete permanen jika diperlukan."
                />

                <Card>
                    <CardContent className="space-y-4">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>ID</TableHead>
                                    <TableHead>Title</TableHead>
                                    <TableHead>Target</TableHead>
                                    <TableHead>Trial</TableHead>
                                    <TableHead>Type</TableHead>
                                    <TableHead>Created At</TableHead>
                                    <TableHead>Action</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {notifications.data.map((notification) => (
                                    <TableRow key={notification.id}>
                                        <TableCell>{notification.id}</TableCell>
                                        <TableCell className="whitespace-normal">
                                            <div>{notification.title}</div>
                                            <div className="text-muted-foreground">
                                                {notification.message}
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            {notification.user?.name ??
                                                notification.role_target ??
                                                '-'}{' '}
                                            {notification.department_target ??
                                                ''}
                                        </TableCell>
                                        <TableCell>
                                            {notification.trial?.trial_code ??
                                                '-'}
                                        </TableCell>
                                        <TableCell>
                                            <Badge variant="outline">
                                                {notification.type}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            {notification.created_at}
                                        </TableCell>
                                        <TableCell>
                                            <ConfirmDialog
                                                trigger={
                                                    <Button
                                                        variant="destructive"
                                                        size="sm"
                                                    >
                                                        Delete
                                                    </Button>
                                                }
                                                title="Delete this notification permanently?"
                                                description={`"${notification.title}" will be permanently removed for every user who can see it. This cannot be undone.`}
                                                confirmLabel="Delete"
                                                formProps={NotificationController.destroy.form(
                                                    notification.id,
                                                )}
                                            />
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {notifications.data.length === 0 && (
                                    <TableRow>
                                        <TableCell
                                            colSpan={7}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada notification.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        <PaginationFooter
                            url={notificationsIndex().url}
                            currentPage={notifications.current_page}
                            lastPage={notifications.last_page}
                            total={notifications.total}
                            itemLabel="notifications"
                        />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

AdminNotificationsIndex.layout = {
    breadcrumbs: [{ title: 'Notifications', href: notificationsIndex() }],
};
