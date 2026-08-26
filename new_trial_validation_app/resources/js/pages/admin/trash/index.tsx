import { Head, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import TrashController from '@/actions/App/Http/Controllers/Admin/TrashController';
import { ConfirmDialog } from '@/components/confirm-dialog';
import Heading from '@/components/heading';
import { PaginationFooter } from '@/components/pagination-footer';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
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
import { trialStatusBadgeClassName } from '@/lib/trial-status';
import { index as trashIndex } from '@/routes/admin/trash';
import type { Paginated } from '@/types';

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

                <Card>
                    <CardContent>
                        <form
                            onSubmit={submit}
                            className="grid gap-4 sm:grid-cols-2 lg:grid-cols-5"
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
                                        setForm({
                                            ...form,
                                            date_to: e.target.value,
                                        })
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
                    </CardContent>
                </Card>

                <Card>
                    <CardContent className="space-y-4">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Trial Code</TableHead>
                                    <TableHead>Product</TableHead>
                                    <TableHead>Product Type</TableHead>
                                    <TableHead>Created By</TableHead>
                                    <TableHead>Deleted By</TableHead>
                                    <TableHead>Deleted At</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Action</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {trials.data.map((trial) => (
                                    <TableRow key={trial.id}>
                                        <TableCell className="font-medium">
                                            {trial.trial_code}
                                        </TableCell>
                                        <TableCell>
                                            {trial.product_name}
                                        </TableCell>
                                        <TableCell>
                                            {trial.product_type}
                                        </TableCell>
                                        <TableCell>
                                            {trial.creator?.name ?? '-'}
                                        </TableCell>
                                        <TableCell>
                                            {trial.deleted_by_user?.name ?? '-'}
                                        </TableCell>
                                        <TableCell>
                                            {trial.deleted_at}
                                        </TableCell>
                                        <TableCell>
                                            <Badge
                                                variant="outline"
                                                className={trialStatusBadgeClassName(
                                                    trial.progress_status,
                                                    trial.final_decision,
                                                )}
                                            >
                                                {trial.progress_status}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            <ConfirmDialog
                                                trigger={
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                    >
                                                        Restore
                                                    </Button>
                                                }
                                                title="Restore this trial?"
                                                description={`${trial.trial_code} will be restored and become visible again in the normal trial list.`}
                                                confirmLabel="Restore"
                                                confirmVariant="default"
                                                formProps={TrashController.restore.form(
                                                    trial.id,
                                                )}
                                            />
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {trials.data.length === 0 && (
                                    <TableRow>
                                        <TableCell
                                            colSpan={8}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Tidak ada trial yang terhapus.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        <PaginationFooter
                            url={trashIndex().url}
                            query={filters}
                            currentPage={trials.current_page}
                            lastPage={trials.last_page}
                            total={trials.total}
                            itemLabel="trials"
                        />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

AdminTrashIndex.layout = {
    breadcrumbs: [{ title: 'Trash', href: trashIndex() }],
};
