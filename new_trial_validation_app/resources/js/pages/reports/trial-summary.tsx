import { Head, Link, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
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
import { TRIAL_STATUSES, trialStatusBadgeClassName } from '@/lib/trial-status';
import { dashboard } from '@/routes';
import { index as reportsIndex, trialSummary } from '@/routes/reports';
import { show as reportShow } from '@/routes/trials/report';
import type { Paginated } from '@/types';

type SummaryItem = {
    id: number;
    trial_code: string;
    product_name: string;
    finish_good_code: string;
    product_type: string;
    validation_scope: string[] | null;
    machine_used: string[] | null;
    progress_status: string;
    final_decision: string | null;
    current_step: string | null;
    created_by: string | null;
    created_at: string | null;
    pending_with: string | null;
};

type Filters = {
    date_from: string;
    date_to: string;
    status: string;
    product_type: string;
    validation_scope: string;
    machine_used: string;
    product_name: string;
};

type PageProps = {
    items: Paginated<SummaryItem>;
    filters: Filters;
    productTypes: string[];
    validationScopes: string[];
    machines: string[];
};

const selectClassName =
    'h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-xs dark:bg-input/30';

export default function ReportsTrialSummary({
    items,
    filters,
    productTypes,
    validationScopes,
    machines,
}: PageProps) {
    const [form, setForm] = useState<Filters>(filters);
    const url = trialSummary().url;

    function submit(e: FormEvent) {
        e.preventDefault();
        router.get(url, form, { preserveState: true, replace: true });
    }

    function reset() {
        router.get(url);
    }

    return (
        <>
            <Head title="Trial Summary Report" />

            <div className="space-y-6 p-4">
                <div className="flex items-center justify-between gap-4 print:hidden">
                    <Heading
                        title="Trial Summary Report"
                        description="Ringkasan semua trial validation."
                    />
                    <Button variant="outline" onClick={() => window.print()}>
                        Print
                    </Button>
                </div>

                <Card className="print:hidden">
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
                                <Label htmlFor="status">Status</Label>
                                <select
                                    id="status"
                                    className={selectClassName}
                                    value={form.status}
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            status: e.target.value,
                                        })
                                    }
                                >
                                    <option value="">Semua status</option>
                                    {TRIAL_STATUSES.map((status) => (
                                        <option key={status} value={status}>
                                            {status}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="product_type">
                                    Product Type
                                </Label>
                                <select
                                    id="product_type"
                                    className={selectClassName}
                                    value={form.product_type}
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            product_type: e.target.value,
                                        })
                                    }
                                >
                                    <option value="">Semua product type</option>
                                    {productTypes.map((type) => (
                                        <option key={type} value={type}>
                                            {type}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="validation_scope">
                                    Validation Scope
                                </Label>
                                <select
                                    id="validation_scope"
                                    className={selectClassName}
                                    value={form.validation_scope}
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            validation_scope: e.target.value,
                                        })
                                    }
                                >
                                    <option value="">Semua scope</option>
                                    {validationScopes.map((scope) => (
                                        <option key={scope} value={scope}>
                                            {scope}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="machine_used">
                                    Machine Used
                                </Label>
                                <select
                                    id="machine_used"
                                    className={selectClassName}
                                    value={form.machine_used}
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            machine_used: e.target.value,
                                        })
                                    }
                                >
                                    <option value="">Semua machine</option>
                                    {machines.map((machine) => (
                                        <option key={machine} value={machine}>
                                            {machine}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="product_name">
                                    Product Name
                                </Label>
                                <Input
                                    id="product_name"
                                    placeholder="Product name"
                                    value={form.product_name}
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            product_name: e.target.value,
                                        })
                                    }
                                />
                            </div>
                            <div className="flex items-end gap-2">
                                <Button type="submit">Search</Button>
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
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Trial ID</TableHead>
                                    <TableHead>Product Name</TableHead>
                                    <TableHead>Finish Good Code</TableHead>
                                    <TableHead>Product Type</TableHead>
                                    <TableHead>Validation Scope</TableHead>
                                    <TableHead>Machine Used</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Current Step</TableHead>
                                    <TableHead>Created By</TableHead>
                                    <TableHead>Created Date</TableHead>
                                    <TableHead>Pending With</TableHead>
                                    <TableHead className="print:hidden">
                                        Action
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {items.data.map((item) => (
                                    <TableRow key={item.id}>
                                        <TableCell>{item.trial_code}</TableCell>
                                        <TableCell>
                                            {item.product_name}
                                        </TableCell>
                                        <TableCell>
                                            {item.finish_good_code}
                                        </TableCell>
                                        <TableCell>
                                            {item.product_type}
                                        </TableCell>
                                        <TableCell>
                                            {(item.validation_scope ?? []).join(
                                                ', ',
                                            )}
                                        </TableCell>
                                        <TableCell>
                                            {(item.machine_used ?? []).join(
                                                ', ',
                                            )}
                                        </TableCell>
                                        <TableCell>
                                            <Badge
                                                variant="outline"
                                                className={trialStatusBadgeClassName(
                                                    item.progress_status,
                                                    item.final_decision,
                                                )}
                                            >
                                                {item.progress_status}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            {item.current_step ?? '-'}
                                        </TableCell>
                                        <TableCell>
                                            {item.created_by ?? '-'}
                                        </TableCell>
                                        <TableCell>
                                            {item.created_at ?? '-'}
                                        </TableCell>
                                        <TableCell>
                                            {item.pending_with ?? '-'}
                                        </TableCell>
                                        <TableCell className="print:hidden">
                                            <Button
                                                variant="link"
                                                size="sm"
                                                asChild
                                            >
                                                <Link
                                                    href={
                                                        reportShow(item.id).url
                                                    }
                                                >
                                                    View Summary
                                                </Link>
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {items.data.length === 0 && (
                                    <TableRow>
                                        <TableCell
                                            colSpan={12}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Tidak ada data trial.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        <PaginationFooter
                            url={url}
                            query={filters}
                            currentPage={items.current_page}
                            lastPage={items.last_page}
                            total={items.total}
                            itemLabel="trials"
                        />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

ReportsTrialSummary.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Report', href: reportsIndex() },
        { title: 'Trial Summary Report', href: trialSummary() },
    ],
};
