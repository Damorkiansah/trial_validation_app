import { router } from '@inertiajs/react';
import {
    createColumnHelper,
    flexRender,
    getCoreRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { trialStatusBadgeClassName } from '@/lib/trial-status';

export type TrialRow = {
    id: number;
    trial_code: string;
    product_name: string;
    finish_good_code: string;
    product_type: string;
    progress_status: string;
    final_decision: string | null;
    current_step: string | null;
    created_at: string;
    pending_with: string | null;
};

export type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
};

const columnHelper = createColumnHelper<TrialRow>();

const columns = [
    columnHelper.accessor('trial_code', { header: 'Trial ID' }),
    columnHelper.accessor('product_name', { header: 'Product Name' }),
    columnHelper.accessor('finish_good_code', { header: 'Finish Good Code' }),
    columnHelper.accessor('product_type', { header: 'Product Type' }),
    columnHelper.accessor('progress_status', {
        header: 'Status',
        cell: (info) => (
            <Badge
                variant="outline"
                className={trialStatusBadgeClassName(
                    info.getValue(),
                    info.row.original.final_decision,
                )}
            >
                {info.getValue()}
            </Badge>
        ),
    }),
    columnHelper.accessor('current_step', {
        header: 'Current Step',
        cell: (info) => info.getValue() ?? '-',
    }),
    columnHelper.accessor('created_at', { header: 'Created Date' }),
    columnHelper.accessor('pending_with', {
        header: 'Pending With',
        cell: (info) => info.getValue() ?? '-',
    }),
];

type TrialsTableProps = {
    trials: Paginated<TrialRow>;
    url: string;
    query: Record<string, string>;
    emptyMessage?: string;
};

export function TrialsTable({
    trials,
    url,
    query,
    emptyMessage = 'Tidak ada trial untuk filter ini.',
}: TrialsTableProps) {
    const table = useReactTable({
        data: trials.data,
        columns,
        getCoreRowModel: getCoreRowModel(),
    });

    function goToPage(page: number) {
        router.get(url, { ...query, page }, { preserveState: true });
    }

    return (
        <div className="space-y-4 rounded-lg border p-4">
            <div className="overflow-x-auto">
                <table className="w-full text-sm">
                    <thead>
                        {table.getHeaderGroups().map((headerGroup) => (
                            <tr
                                key={headerGroup.id}
                                className="border-b text-left"
                            >
                                {headerGroup.headers.map((header) => (
                                    <th key={header.id} className="p-2">
                                        {flexRender(
                                            header.column.columnDef.header,
                                            header.getContext(),
                                        )}
                                    </th>
                                ))}
                            </tr>
                        ))}
                    </thead>
                    <tbody>
                        {table.getRowModel().rows.map((row) => (
                            <tr key={row.id} className="border-b align-top">
                                {row.getVisibleCells().map((cell) => (
                                    <td key={cell.id} className="p-2">
                                        {flexRender(
                                            cell.column.columnDef.cell,
                                            cell.getContext(),
                                        )}
                                    </td>
                                ))}
                            </tr>
                        ))}
                        {trials.data.length === 0 && (
                            <tr>
                                <td
                                    colSpan={columns.length}
                                    className="p-4 text-center text-muted-foreground"
                                >
                                    {emptyMessage}
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
                        onClick={() => goToPage(trials.current_page - 1)}
                    >
                        Previous
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        disabled={trials.current_page >= trials.last_page}
                        onClick={() => goToPage(trials.current_page + 1)}
                    >
                        Next
                    </Button>
                </div>
            </div>
        </div>
    );
}
