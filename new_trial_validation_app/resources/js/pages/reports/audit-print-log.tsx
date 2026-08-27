import { Head } from '@inertiajs/react';
import Heading from '@/components/heading';
import { PaginationFooter } from '@/components/pagination-footer';
import { Card, CardContent } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { dashboard } from '@/routes';
import { auditPrintLog, index as reportsIndex } from '@/routes/reports';
import type { Paginated } from '@/types';

type AuditPrintLogItem = {
    id: number;
    trial: { id: number; trial_code: string } | null;
    user_email: string | null;
    created_at: string | null;
    new_data: { report_type?: string } | null;
};

type PageProps = {
    items: Paginated<AuditPrintLogItem>;
};

export default function ReportsAuditPrintLog({ items }: PageProps) {
    return (
        <>
            <Head title="Audit Print Log" />

            <div className="space-y-6 p-4">
                <Heading
                    title="Audit Print Log"
                    description="Log aktivitas print report jika tersedia."
                />

                <Card>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Trial ID</TableHead>
                                    <TableHead>Printed By</TableHead>
                                    <TableHead>Printed At</TableHead>
                                    <TableHead>Report Type</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {items.data.map((item) => (
                                    <TableRow key={item.id}>
                                        <TableCell>
                                            {item.trial?.trial_code ?? '-'}
                                        </TableCell>
                                        <TableCell>
                                            {item.user_email ?? '-'}
                                        </TableCell>
                                        <TableCell>
                                            {item.created_at ?? '-'}
                                        </TableCell>
                                        <TableCell>
                                            {item.new_data?.report_type ??
                                                'Report'}
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {items.data.length === 0 && (
                                    <TableRow>
                                        <TableCell
                                            colSpan={4}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada audit print log.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        <PaginationFooter
                            url={auditPrintLog().url}
                            currentPage={items.current_page}
                            lastPage={items.last_page}
                            total={items.total}
                            itemLabel="log"
                        />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

ReportsAuditPrintLog.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Report', href: reportsIndex() },
        { title: 'Audit Print Log', href: auditPrintLog() },
    ],
};
