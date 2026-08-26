import { Form, Head, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { Fragment, useState } from 'react';
import ActivityLogController from '@/actions/App/Http/Controllers/Admin/ActivityLogController';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
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
import { index as activityLogsIndex } from '@/routes/admin/activity-logs';

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

type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
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

function DeleteLogButton({ log }: { log: ActivityLogItem }) {
    return (
        <Dialog>
            <DialogTrigger asChild>
                <Button variant="destructive" size="sm">
                    Delete
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>Delete this activity log permanently?</DialogTitle>
                <DialogDescription>
                    This entry will be permanently removed. This cannot be
                    undone.
                </DialogDescription>
                <DialogFooter className="gap-2">
                    <DialogClose asChild>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Form
                        {...ActivityLogController.destroy.form(log.id)}
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
        if (selected.length === 0) {
            return;
        }

        if (
            !confirm(
                `Delete ${selected.length} selected activity log(s) permanently?`,
            )
        ) {
            return;
        }

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

                <form
                    onSubmit={submit}
                    className="grid gap-4 rounded-lg border p-4 sm:grid-cols-2 lg:grid-cols-4"
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
                                setForm({ ...form, date_to: e.target.value })
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
                                setForm({ ...form, user: e.target.value })
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
                                setForm({ ...form, role: e.target.value })
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
                                setForm({ ...form, module: e.target.value })
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
                                setForm({ ...form, action: e.target.value })
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

                <div className="space-y-4 rounded-lg border p-4">
                    <div className="flex items-center justify-between">
                        <h2 className="text-sm font-medium">
                            Activity Log Data
                        </h2>
                        <Button
                            variant="destructive"
                            size="sm"
                            disabled={selected.length === 0}
                            onClick={deleteSelected}
                        >
                            Delete Selected
                        </Button>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b text-left">
                                    <th className="p-2">
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
                                    </th>
                                    <th className="p-2">Date/Time</th>
                                    <th className="p-2">User</th>
                                    <th className="p-2">Role</th>
                                    <th className="p-2">Action</th>
                                    <th className="p-2">Module</th>
                                    <th className="p-2">Record</th>
                                    <th className="p-2">IP Address</th>
                                    <th className="p-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {logs.data.map((log) => (
                                    <Fragment key={log.id}>
                                        <tr className="border-b align-top">
                                            <td className="p-2">
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
                                            </td>
                                            <td className="p-2">
                                                {log.created_at}
                                            </td>
                                            <td className="p-2">
                                                {log.user_name ?? '-'}
                                            </td>
                                            <td className="p-2">
                                                {log.user_role ?? '-'}
                                            </td>
                                            <td className="p-2">
                                                {log.action}
                                            </td>
                                            <td className="p-2">
                                                {log.module}
                                            </td>
                                            <td className="p-2">
                                                {log.record_label ??
                                                    log.record_id ??
                                                    '-'}
                                            </td>
                                            <td className="p-2">
                                                {log.ip_address ?? '-'}
                                            </td>
                                            <td className="p-2">
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
                                                    <DeleteLogButton
                                                        log={log}
                                                    />
                                                </div>
                                            </td>
                                        </tr>
                                        {expanded === log.id && (
                                            <tr className="border-b bg-muted/30">
                                                <td colSpan={9} className="p-4">
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
                                                </td>
                                            </tr>
                                        )}
                                    </Fragment>
                                ))}
                                {logs.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={9}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada activity log.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    <div className="flex items-center justify-between text-sm text-muted-foreground">
                        <span>
                            Page {logs.current_page} of {logs.last_page} (
                            {logs.total} logs)
                        </span>
                        <div className="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={logs.current_page <= 1}
                                onClick={() =>
                                    router.get(activityLogsIndex().url, {
                                        ...filters,
                                        page: logs.current_page - 1,
                                    })
                                }
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={logs.current_page >= logs.last_page}
                                onClick={() =>
                                    router.get(activityLogsIndex().url, {
                                        ...filters,
                                        page: logs.current_page + 1,
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

AdminActivityLogsIndex.layout = {
    breadcrumbs: [{ title: 'Activity Logs', href: activityLogsIndex() }],
};
