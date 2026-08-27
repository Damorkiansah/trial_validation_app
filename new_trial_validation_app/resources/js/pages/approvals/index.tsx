import { Head, Link } from '@inertiajs/react';
import TrialReportController from '@/actions/App/Http/Controllers/TrialReportController';
import Heading from '@/components/heading';
import { PaginationFooter } from '@/components/pagination-footer';
import { Badge } from '@/components/ui/badge';
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
import { trialStatusBadgeClassName } from '@/lib/trial-status';
import { index as approvalsIndex } from '@/routes/approvals';
import type { Paginated } from '@/types';

type ApprovalItem = {
    id: number;
    trial_code: string;
    product_name: string;
    product_type: string;
    progress_status: string;
    final_decision: string | null;
    updated_at: string | null;
    approver: { id: number; name: string; email: string } | null;
};

type PageProps = {
    items: Paginated<ApprovalItem>;
};

export default function ApprovalsIndex({ items }: PageProps) {
    return (
        <>
            <Head title="Approval Queue" />

            <div className="space-y-6 p-4">
                <Heading
                    title="Approval Queue"
                    description="Trial yang menunggu keputusan final Manager QAC / approver."
                />

                <Card>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Trial</TableHead>
                                    <TableHead>Product</TableHead>
                                    <TableHead>Product Type</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Approver</TableHead>
                                    <TableHead></TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {items.data.map((item) => (
                                    <TableRow key={item.id}>
                                        <TableCell>
                                            <Link
                                                href={
                                                    TrialReportController.show(
                                                        item.id,
                                                    ).url
                                                }
                                                className="font-medium underline"
                                            >
                                                {item.trial_code}
                                            </Link>
                                        </TableCell>
                                        <TableCell>
                                            {item.product_name}
                                        </TableCell>
                                        <TableCell>
                                            {item.product_type}
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
                                            {item.approver
                                                ? (item.approver.name ??
                                                  item.approver.email)
                                                : '-'}
                                        </TableCell>
                                        <TableCell>
                                            <Button size="sm" asChild>
                                                <Link
                                                    href={
                                                        TrialReportController.show(
                                                            item.id,
                                                        ).url
                                                    }
                                                >
                                                    Tinjau & Putuskan
                                                </Link>
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {items.data.length === 0 && (
                                    <TableRow>
                                        <TableCell
                                            colSpan={6}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Tidak ada trial yang menunggu
                                            approval.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        <PaginationFooter
                            url={approvalsIndex().url}
                            query={{}}
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

ApprovalsIndex.layout = {
    breadcrumbs: [{ title: 'Approval Queue', href: approvalsIndex() }],
};
