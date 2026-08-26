import { Form, Head, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import TrashController from '@/actions/App/Http/Controllers/Admin/TrashController';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { trialStatusBadgeClassName } from '@/lib/trial-status';
import { index as trashIndex } from '@/routes/admin/trash';

type TrialItem = {
    id: number;
    trial_code: string;
    product_name: string;
    product_type: string;
    progress_status: string;
    final_decision: string | null;
    deleted_at: string;
    creator: { id: number; name: string; email: string } | null;
    deleted_by_user: { id: number; name: string; email: string } | null;
};

type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
};

type Filters = {
    q: string;
    deleted_by: string;
    date_from: string;
    date_to: string;
};

type PageProps = {
    trials: Paginated<TrialItem>;
    filters: Filters;
};

function RestoreTrialButton({ trial }: { trial: TrialItem }) {
    return (
        <Form
            {...TrashController.restore.form(trial.id)}
            options={{ preserveScroll: true }}
        >
            {({ processing }) => (
                <Button
                    type="submit"
                    variant="outline"
                    size="sm"
                    disabled={processing}
                    onClick={(e) => {
                        if (!confirm(`Restore trial ${trial.trial_code}?`)) {
                            e.preventDefault();
                        }
                    }}
                >
                    Restore
                </Button>
            )}
        </Form>
    );
}

export default function AdminTrashIndex({ trials, filters }: PageProps) {
    const [form, setForm] = useState<Filters>(filters);

    function submit(e: FormEvent) {
        e.preventDefault();
        router.get(trashIndex().url, form, {
            preserveState: true,
            replace: true,
        });
    }

    function reset() {
        router.get(trashIndex().url);
    }

    return (
        <>
            <Head title="Trash" />

            <div className="space-y-6 p-4">
                <Heading
                    title="Trash - Deleted Trials"
                    description="Restore trial yang sudah terhapus. Hapus permanen belum tersedia di aplikasi baru ini."
                />

                <form
                    onSubmit={submit}
                    className="grid gap-4 rounded-lg border p-4 sm:grid-cols-2 lg:grid-cols-5"
                >
                    <div className="grid gap-2 lg:col-span-2">
                        <Label htmlFor="q">Search</Label>
                        <Input
                            id="q"
                            placeholder="Trial code, product..."
                            value={form.q}
                            onChange={(e) =>
                                setForm({ ...form, q: e.target.value })
                            }
                        />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="deleted_by">Deleted by</Label>
                        <Input
                            id="deleted_by"
                            placeholder="Nama user..."
                            value={form.deleted_by}
                            onChange={(e) =>
                                setForm({
                                    ...form,
                                    deleted_by: e.target.value,
                                })
                            }
                        />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="date_from">From</Label>
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
                        <Label htmlFor="date_to">To</Label>
                        <Input
                            id="date_to"
                            type="date"
                            value={form.date_to}
                            onChange={(e) =>
                                setForm({ ...form, date_to: e.target.value })
                            }
                        />
                    </div>
                    <div className="flex items-end gap-2 lg:col-span-5">
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
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b text-left">
                                    <th className="p-2">Trial Code</th>
                                    <th className="p-2">Product</th>
                                    <th className="p-2">Product Type</th>
                                    <th className="p-2">Created By</th>
                                    <th className="p-2">Deleted By</th>
                                    <th className="p-2">Deleted At</th>
                                    <th className="p-2">Status</th>
                                    <th className="p-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {trials.data.map((trial) => (
                                    <tr
                                        key={trial.id}
                                        className="border-b align-top"
                                    >
                                        <td className="p-2 font-medium">
                                            {trial.trial_code}
                                        </td>
                                        <td className="p-2">
                                            {trial.product_name}
                                        </td>
                                        <td className="p-2">
                                            {trial.product_type}
                                        </td>
                                        <td className="p-2">
                                            {trial.creator?.name ?? '-'}
                                        </td>
                                        <td className="p-2">
                                            {trial.deleted_by_user?.name ?? '-'}
                                        </td>
                                        <td className="p-2">
                                            {trial.deleted_at}
                                        </td>
                                        <td className="p-2">
                                            <Badge
                                                variant="outline"
                                                className={trialStatusBadgeClassName(
                                                    trial.progress_status,
                                                    trial.final_decision,
                                                )}
                                            >
                                                {trial.progress_status}
                                            </Badge>
                                        </td>
                                        <td className="p-2">
                                            <RestoreTrialButton trial={trial} />
                                        </td>
                                    </tr>
                                ))}
                                {trials.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={8}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Tidak ada trial yang terhapus.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    <div className="flex items-center justify-between text-sm text-muted-foreground">
                        <span>
                            Page {trials.current_page} of {trials.last_page} (
                            {trials.total} trials)
                        </span>
                        <div className="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={trials.current_page <= 1}
                                onClick={() =>
                                    router.get(trashIndex().url, {
                                        ...filters,
                                        page: trials.current_page - 1,
                                    })
                                }
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={
                                    trials.current_page >= trials.last_page
                                }
                                onClick={() =>
                                    router.get(trashIndex().url, {
                                        ...filters,
                                        page: trials.current_page + 1,
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

AdminTrashIndex.layout = {
    breadcrumbs: [{ title: 'Trash', href: trashIndex() }],
};
