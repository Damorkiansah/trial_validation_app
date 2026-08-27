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
import { approved, index as reportsIndex } from '@/routes/reports';
import { show as reportShow } from '@/routes/trials/report';
import type { Paginated } from '@/types';

type ApprovedItem = {
    id: number;
    trial_code: string;
    product_name: string;
    finish_good_code: string;
    product_type: string;
    approved_at: string | null;
    approved_by: string | null;
};

type PageProps = {
    items: Paginated<ApprovedItem>;
};

export default function ReportsApproved({ items }: PageProps) {
    return (
        <>
            <Head title="Approved Report" />

            <div className="space-y-6 p-4">
                <div className="flex items-center justify-between gap-4 print:hidden">
                    <Heading
                        title="Approved Report"
                        description="Trial dengan status Approved."
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
                                    <TableHead>Approved Date</TableHead>
                                    <TableHead>Approved By</TableHead>
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
                                            {item.approved_at ?? '-'}
                                        </TableCell>
                                        <TableCell>
                                            {item.approved_by ?? '-'}
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
                                            colSpan={7}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada approved report.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        <PaginationFooter
                            url={approved().url}
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

ReportsApproved.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Report', href: reportsIndex() },
        { title: 'Approved Report', href: approved() },
    ],
};
