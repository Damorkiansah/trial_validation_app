import { Head, Link, router } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useState } from 'react';
import Heading from '@/components/heading';
import { TrialsTable } from '@/components/trials-table';
import type { TrialRow } from '@/components/trials-table';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes';
import { create as createTrial, index as trialsIndex } from '@/routes/trials';
import type { Paginated } from '@/types';

type Filters = {
    q: string;
    product_type: string;
    date_from: string;
    date_to: string;
};

type PageProps = {
    trials: Paginated<TrialRow>;
    filters: Filters;
    productTypes: string[];
    pageTitle: string;
    pageSubtitle: string;
    group: string;
    canCreateTrial: boolean;
};

export default function TrialsIndex({
    trials,
    filters,
    productTypes,
    pageTitle,
    pageSubtitle,
    group,
    canCreateTrial,
}: PageProps) {
    const [form, setForm] = useState<Filters>(filters);
    const url = trialsIndex(group).url;

    function submit(e: FormEvent) {
        e.preventDefault();
        router.get(url, form, { preserveState: true, replace: true });
    }

    function reset() {
        router.get(url);
    }

    return (
        <>
            <Head title={pageTitle} />

            <div className="space-y-6 p-4">
                <div className="flex items-start justify-between gap-4">
                    <Heading title={pageTitle} description={pageSubtitle} />
                    {canCreateTrial && (
                        <Button asChild>
                            <Link href={createTrial().url}>New Trial</Link>
                        </Button>
                    )}
                </div>

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
                                    placeholder="Trial, product, FG code, scope, machine"
                                    value={form.q}
                                    onChange={(e) =>
                                        setForm({ ...form, q: e.target.value })
                                    }
                                />
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
                    url={url}
                    query={filters}
                    emptyMessage="Tidak ada trial pada halaman ini."
                />
            </div>
        </>
    );
}

TrialsIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Trials', href: '#' },
    ],
};
