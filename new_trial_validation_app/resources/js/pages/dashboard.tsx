import { Head, Link, router } from '@inertiajs/react';
import {
    AlertTriangle,
    CheckCircle2,
    Clock,
    FileEdit,
    ListChecks,
    Search,
    XCircle,
} from 'lucide-react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import Heading from '@/components/heading';
import { TrialsTable } from '@/components/trials-table';
import type { TrialRow } from '@/components/trials-table';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { TRIAL_STATUSES } from '@/lib/trial-status';
import { dashboard } from '@/routes';
import { create as createTrial, index as trialsIndex } from '@/routes/trials';
import type { Paginated } from '@/types';

type Summary = {
    total: number;
    draft: number;
    in_review: number;
    ready: number;
    approved: number;
    need_revision: number;
    rejected: number;
};

type Filters = {
    q: string;
    product_type: string;
    date_from: string;
    date_to: string;
    status: string;
};

type PageProps = {
    trials: Paginated<TrialRow>;
    filters: Filters;
    productTypes: string[];
    summary: Summary;
    canCreateTrial: boolean;
};

const summaryCards: {
    key: keyof Summary;
    label: string;
    href: () => string;
    icon: typeof ListChecks;
}[] = [
    {
        key: 'total',
        label: 'Total Trials',
        href: () => dashboard().url,
        icon: ListChecks,
    },
    {
        key: 'draft',
        label: 'Draft',
        href: () => dashboard({ query: { status: 'Draft' } }).url,
        icon: FileEdit,
    },
    {
        key: 'in_review',
        label: 'In Review',
        href: () => trialsIndex('in-review').url,
        icon: Search,
    },
    {
        key: 'ready',
        label: 'Ready for Approval',
        href: () => trialsIndex('waiting-approval').url,
        icon: Clock,
    },
    {
        key: 'approved',
        label: 'Approved',
        href: () => trialsIndex('approved').url,
        icon: CheckCircle2,
    },
    {
        key: 'need_revision',
        label: 'Need Revision',
        href: () => trialsIndex('need-revision').url,
        icon: AlertTriangle,
    },
    {
        key: 'rejected',
        label: 'Rejected',
        href: () => trialsIndex('rejected').url,
        icon: XCircle,
    },
];

export default function Dashboard({
    trials,
    filters,
    productTypes,
    summary,
    canCreateTrial,
}: PageProps) {
    const [form, setForm] = useState<Filters>(filters);

    function submit(e: FormEvent) {
        e.preventDefault();
        router.get(dashboard().url, form, {
            preserveState: true,
            replace: true,
        });
    }

    function reset() {
        router.get(dashboard().url);
    }

    return (
        <>
            <Head title="Dashboard" />

            <div className="space-y-6 p-4">
                <div className="flex items-start justify-between gap-4">
                    <Heading
                        title="Trial Dashboard"
                        description="Kelola dan pantau proses trial validation."
                    />
                    {canCreateTrial && (
                        <Button asChild>
                            <Link href={createTrial().url}>New Trial</Link>
                        </Button>
                    )}
                </div>

                <section className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7">
                    {summaryCards.map((card) => (
                        <a key={card.key} href={card.href()}>
                            <Card
                                className={
                                    'gap-2 py-4 transition-colors hover:bg-muted' +
                                    (card.key === 'total'
                                        ? ' border-l-4 border-l-brand'
                                        : '')
                                }
                            >
                                <CardContent className="flex items-center justify-between px-4">
                                    <div>
                                        <span className="text-sm text-muted-foreground">
                                            {card.label}
                                        </span>
                                        <div className="text-2xl font-semibold">
                                            {summary[card.key]}
                                        </div>
                                    </div>
                                    <card.icon className="size-5 text-muted-foreground" />
                                </CardContent>
                            </Card>
                        </a>
                    ))}
                </section>

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
                                    placeholder="Trial, product, FG code, category, scope, machine"
                                    value={form.q}
                                    onChange={(e) =>
                                        setForm({ ...form, q: e.target.value })
                                    }
                                />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="status">Status</Label>
                                <select
                                    id="status"
                                    className="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-xs dark:bg-input/30"
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
                                    className="h-9 rounded-md border border-input bg-transparent px-3 text-sm shadow-xs dark:bg-input/30"
                                    value={form.product_type}
                                    onChange={(e) =>
                                        setForm({
                                            ...form,
                                            product_type: e.target.value,
                                        })
                                    }
                                >
                                    <option value="">Semua kategori</option>
                                    {productTypes.map((type) => (
                                        <option key={type} value={type}>
                                            {type}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="date_from">Tanggal Dari</Label>
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
                                <Label htmlFor="date_to">Tanggal Sampai</Label>
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

                <TrialsTable
                    trials={trials}
                    url={dashboard().url}
                    query={filters}
                    emptyMessage="Tidak ada trial untuk filter ini."
                />
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [
        {
            title: 'Dashboard',
            href: dashboard(),
        },
    ],
};
