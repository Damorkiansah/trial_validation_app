import {
    createColumnHelper,
    flexRender,
    getCoreRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { PaginationFooter } from '@/components/pagination-footer';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { trialStatusBadgeClassName } from '@/lib/trial-status';
import type { Paginated } from '@/types';

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

    return (
        <Card>
            <CardContent className="space-y-4">
                <Table>
                    <TableHeader>
                        {table.getHeaderGroups().map((headerGroup) => (
                            <TableRow key={headerGroup.id}>
                                {headerGroup.headers.map((header) => (
                                    <TableHead key={header.id}>
                                        {flexRender(
                                            header.column.columnDef.header,
                                            header.getContext(),
                                        )}
                                    </TableHead>
                                ))}
                            </TableRow>
                        ))}
                    </TableHeader>
                    <TableBody>
                        {table.getRowModel().rows.map((row) => (
                            <TableRow key={row.id} className="align-top">
                                {row.getVisibleCells().map((cell) => (
                                    <TableCell key={cell.id}>
                                        {flexRender(
                                            cell.column.columnDef.cell,
                                            cell.getContext(),
                                        )}
                                    </TableCell>
                                ))}
                            </TableRow>
                        ))}
                        {trials.data.length === 0 && (
                            <TableRow>
                                <TableCell
                                    colSpan={columns.length}
                                    className="p-4 text-center text-muted-foreground"
                                >
                                    {emptyMessage}
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>

                <PaginationFooter
                    url={url}
                    query={query}
                    currentPage={trials.current_page}
                    lastPage={trials.last_page}
                    total={trials.total}
                    itemLabel="trials"
                />
            </CardContent>
        </Card>
    );
}
