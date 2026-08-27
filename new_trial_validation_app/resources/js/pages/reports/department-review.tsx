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
import { departmentReview, index as reportsIndex } from '@/routes/reports';
import { show as reportShow } from '@/routes/trials/report';
import type { Paginated } from '@/types';

type DepartmentReviewItem = {
    id: number;
    trial_code: string;
    product_name: string;
    pending_with: string | null;
    departments: Record<string, string>;
    review_status: string;
};

type PageProps = {
    items: Paginated<DepartmentReviewItem>;
    reviewerDepartments: string[];
};

export default function ReportsDepartmentReview({
    items,
    reviewerDepartments,
}: PageProps) {
    return (
        <>
            <Head title="Department Review Report" />

            <div className="space-y-6 p-4">
                <div className="flex items-center justify-between gap-4 print:hidden">
                    <Heading
                        title="Department Review Report"
                        description="Progress review per department."
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
                                    {reviewerDepartments.map((dept) => (
                                        <TableHead key={dept}>{dept}</TableHead>
                                    ))}
                                    <TableHead>Review Status</TableHead>
                                    <TableHead>Pending Department</TableHead>
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
                                        {reviewerDepartments.map((dept) => (
                                            <TableCell key={dept}>
                                                {item.departments[dept] ??
                                                    'N/A'}
                                            </TableCell>
                                        ))}
                                        <TableCell>
                                            {item.review_status}
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
                                                    View Review
                                                </Link>
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {items.data.length === 0 && (
                                    <TableRow>
                                        <TableCell
                                            colSpan={
                                                reviewerDepartments.length + 5
                                            }
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada data review department.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        <PaginationFooter
                            url={departmentReview().url}
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

ReportsDepartmentReview.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Report', href: reportsIndex() },
        { title: 'Department Review Report', href: departmentReview() },
    ],
};
