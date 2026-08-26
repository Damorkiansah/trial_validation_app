import { Head, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Fragment, useState } from 'react';
import ActivityLogController from '@/actions/App/Http/Controllers/Admin/ActivityLogController';
import { ConfirmDialog } from '@/components/confirm-dialog';
import Heading from '@/components/heading';
import { PaginationFooter } from '@/components/pagination-footer';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { index as activityLogsIndex } from '@/routes/admin/activity-logs';
import type { Paginated } from '@/types';

type ActivityLogItem = {
    id: number;
    created_at: string;
    user_name: string | null;
    user_role: string | null;
    action: string;
    module: string;
    record_id: string | null;
    record_label: string | null;
    old_data: string | null;
    new_data: string | null;
    ip_address: string | null;
};

type Filters = {
    date_from: string;
    date_to: string;
    user: string;
    role: string;
    module: string;
    action: string;
    q: string;
};

type PageProps = {
    logs: Paginated<ActivityLogItem>;
    filters: Filters;
};

export default function AdminActivityLogsIndex({ logs, filters }: PageProps) {
    const [form, setForm] = useState<Filters>(filters);
    const [selected, setSelected] = useState<number[]>([]);
    const [expanded, setExpanded] = useState<number | null>(null);

    function submit(e: FormEvent) {
        e.preventDefault();
        router.get(activityLogsIndex().url, form, {
            preserveState: true,
            replace: true,
        });
    }

    function reset() {
        router.get(activityLogsIndex().url);
    }

    function toggleSelected(id: number, checked: boolean) {
        setSelected((prev) =>
            checked ? [...prev, id] : prev.filter((x) => x !== id),
        );
    }

    function toggleSelectAll(checked: boolean) {
        setSelected(checked ? logs.data.map((log) => log.id) : []);
    }

    function deleteSelected() {
        router.post(
            ActivityLogController.destroySelected().url,
            { log_ids: selected },
            {
                preserveScroll: true,
                onSuccess: () => setSelected([]),
            },
        );
    }

    return (
        <>
            <Head title="Activity Logs" />

            <div className="space-y-6 p-4">
                <Heading
                    title="Activity Logs"
                    description="Riwayat action penting pada aplikasi."
                />

                <Card>
                    <CardContent>
                        <form
                            onSubmit={submit}
                            className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4"
                        >
                            <div className="grid gap-2">
                                <Label htmlFor="date_from">Date From</Label>
                                <Input
                                    id="date_from"
                                    type="date"
                                    value={form.date_from}
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            date_from: e.target.value,
                                        })
                                    }
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="date_to">Date To</Label>
                                <Input
                                    id="date_to"
                                    type="date"
                                    value={form.date_to}
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            date_to: e.target.value,
                                        })
                                    }
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="user">User</Label>
                                <Input
                                    id="user"
                                    placeholder="User"
                                    value={form.user}
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            user: e.target.value,
                                        })
                                    }
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="role">Role</Label>
                                <Input
                                    id="role"
                                    placeholder="Role"
                                    value={form.role}
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            role: e.target.value,
                                        })
                                    }
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="module">Module</Label>
                                <Input
                                    id="module"
                                    placeholder="Module"
                                    value={form.module}
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            module: e.target.value,
                                        })
                                    }
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="action">Action</Label>
                                <Input
                                    id="action"
                                    placeholder="Action"
                                    value={form.action}
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            action: e.target.value,
                                        })
                                    }
                                />
                            </div>
                            <div className="grid gap-2 sm:col-span-2">
                                <Label htmlFor="q">Search</Label>
                                <Input
                                    id="q"
                                    placeholder="Keyword"
                                    value={form.q}
                                    onChange={(e) =>
                                        setForm({ ...form, q: e.target.value })
                                    }
                                />
                            </div>
                            <div className="flex items-end gap-2 lg:col-span-4">
                                <Button type="submit">Filter</Button>
                                <Button
                                    type="button"
                                    variant="secondary"
                                    onClick={reset}
                                >
                                    Reset
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader className="flex-row items-center justify-between">
                        <h2 className="text-sm font-medium">
                            Activity Log Data
                        </h2>
                        <Dialog>
                            <DialogTrigger asChild>
                                <Button
                                    variant="destructive"
                                    size="sm"
                                    disabled={selected.length === 0}
                                >
                                    Delete Selected
                                    {selected.length > 0
                                        ? ` (${selected.length})`
                                        : ''}
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogTitle>
                                    Delete {selected.length} activity log(s)
                                    permanently?
                                </DialogTitle>
                                <DialogDescription>
                                    These entries will be permanently removed.
                                    This cannot be undone.
                                </DialogDescription>
                                <DialogFooter className="gap-2">
                                    <DialogClose asChild>
                                        <Button variant="secondary">
                                            Cancel
                                        </Button>
                                    </DialogClose>
                                    <DialogClose asChild>
                                        <Button
                                            variant="destructive"
                                            onClick={deleteSelected}
                                        >
                                            Delete
                                        </Button>
                                    </DialogClose>
                                </DialogFooter>
                            </DialogContent>
                        </Dialog>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>
                                        <Checkbox
                                            aria-label="Select all activity logs"
                                            checked={
                                                logs.data.length > 0 &&
                                                selected.length ===
                                                    logs.data.length
                                            }
                                            onCheckedChange={(checked) =>
                                                toggleSelectAll(
                                                    checked === true,
                                                )
                                            }
                                        />
                                    </TableHead>
                                    <TableHead>Date/Time</TableHead>
                                    <TableHead>User</TableHead>
                                    <TableHead>Role</TableHead>
                                    <TableHead>Action</TableHead>
                                    <TableHead>Module</TableHead>
                                    <TableHead>Record</TableHead>
                                    <TableHead>IP Address</TableHead>
                                    <TableHead>Action</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {logs.data.map((log) => (
                                    <Fragment key={log.id}>
                                        <TableRow>
                                            <TableCell>
                                                <Checkbox
                                                    aria-label="Select activity log"
                                                    checked={selected.includes(
                                                        log.id,
                                                    )}
                                                    onCheckedChange={(
                                                        checked,
                                                    ) =>
                                                        toggleSelected(
                                                            log.id,
                                                            checked === true,
                                                        )
                                                    }
                                                />
                                            </TableCell>
                                            <TableCell>
                                                {log.created_at}
                                            </TableCell>
                                            <TableCell>
                                                {log.user_name ?? '-'}
                                            </TableCell>
                                            <TableCell>
                                                {log.user_role ?? '-'}
                                            </TableCell>
                                            <TableCell>{log.action}</TableCell>
                                            <TableCell>{log.module}</TableCell>
                                            <TableCell>
                                                {log.record_label ??
                                                    log.record_id ??
                                                    '-'}
                                            </TableCell>
                                            <TableCell>
                                                {log.ip_address ?? '-'}
                                            </TableCell>
                                            <TableCell>
                                                <div className="flex gap-2">
                                                    <Button
                                                        type="button"
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() =>
                                                            setExpanded(
                                                                expanded ===
                                                                    log.id
                                                                    ? null
                                                                    : log.id,
                                                            )
                                                        }
                                                    >
                                                        Detail
                                                    </Button>
                                                    <ConfirmDialog
                                                        trigger={
                                                            <Button
                                                                variant="destructive"
                                                                size="sm"
                                                            >
                                                                Delete
                                                            </Button>
                                                        }
                                                        title="Delete this activity log permanently?"
                                                        description="This entry will be permanently removed. This cannot be undone."
                                                        confirmLabel="Delete"
                                                        formProps={ActivityLogController.destroy.form(
                                                            log.id,
                                                        )}
                                                    />
                                                </div>
                                            </TableCell>
                                        </TableRow>
                                        {expanded === log.id && (
                                            <TableRow className="bg-muted/30">
                                                <TableCell
                                                    colSpan={9}
                                                    className="p-4 whitespace-normal"
                                                >
                                                    <div className="grid gap-4 sm:grid-cols-2">
                                                        <div>
                                                            <h3 className="mb-1 text-xs font-medium text-muted-foreground">
                                                                Old Data
                                                            </h3>
                                                            <pre className="overflow-x-auto rounded bg-background p-2 text-xs">
                                                                {log.old_data ??
                                                                    'null'}
                                                            </pre>
                                                        </div>
                                                        <div>
                                                            <h3 className="mb-1 text-xs font-medium text-muted-foreground">
                                                                New Data
                                                            </h3>
                                                            <pre className="overflow-x-auto rounded bg-background p-2 text-xs">
                                                                {log.new_data ??
                                                                    'null'}
                                                            </pre>
                                                        </div>
                                                    </div>
                                                </TableCell>
                                            </TableRow>
                                        )}
                                    </Fragment>
                                ))}
                                {logs.data.length === 0 && (
                                    <TableRow>
                                        <TableCell
                                            colSpan={9}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada activity log.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        <PaginationFooter
                            url={activityLogsIndex().url}
                            query={filters}
                            currentPage={logs.current_page}
                            lastPage={logs.last_page}
                            total={logs.total}
                            itemLabel="logs"
                        />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

AdminActivityLogsIndex.layout = {
    breadcrumbs: [{ title: 'Activity Logs', href: activityLogsIndex() }],
};
