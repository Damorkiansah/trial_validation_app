import { Head, Link } from '@inertiajs/react';
import Heading from '@/components/heading';
import { PaginationFooter } from '@/components/pagination-footer';
import { Button } from '@/components/ui/button';
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
import { index as reportsIndex, rejected } from '@/routes/reports';
import { show as reportShow } from '@/routes/trials/report';
import type { Paginated } from '@/types';

type RejectedItem = {
    id: number;
    trial_code: string;
    product_name: string;
    finish_good_code: string;
    product_type: string;
    rejected_at: string | null;
    rejected_by: string | null;
    approval_comment: string | null;
};

type PageProps = {
    items: Paginated<RejectedItem>;
};

export default function ReportsRejected({ items }: PageProps) {
    return (
        <>
            <Head title="Rejected Report" />

            <div className="space-y-6 p-4">
                <div className="flex items-center justify-between gap-4 print:hidden">
                    <Heading
                        title="Rejected Report"
                        description="Trial yang ditolak final oleh Manager QAC."
                    />
                    <Button variant="outline" onClick={() => window.print()}>
                        Print
                    </Button>
                </div>

                <Card>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Trial ID</TableHead>
                                    <TableHead>Product Name</TableHead>
                                    <TableHead>Finish Good Code</TableHead>
                                    <TableHead>Product Type</TableHead>
                                    <TableHead>Rejected Date</TableHead>
                                    <TableHead>Rejected By</TableHead>
                                    <TableHead>Reason / Final Remark</TableHead>
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
                                            {item.rejected_at ?? '-'}
                                        </TableCell>
                                        <TableCell>
                                            {item.rejected_by ?? '-'}
                                        </TableCell>
                                        <TableCell>
                                            {item.approval_comment ?? '-'}
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
                                                    View Report
                                                </Link>
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {items.data.length === 0 && (
                                    <TableRow>
                                        <TableCell
                                            colSpan={8}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada rejected report.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        <PaginationFooter
                            url={rejected().url}
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

ReportsRejected.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Report', href: reportsIndex() },
        { title: 'Rejected Report', href: rejected() },
    ],
};
