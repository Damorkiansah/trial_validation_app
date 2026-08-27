import { Head, Link } from '@inertiajs/react';
import ApprovalController from '@/actions/App/Http/Controllers/ApprovalController';
import { ConfirmDialog } from '@/components/confirm-dialog';
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
import { Textarea } from '@/components/ui/textarea';
import { trialStatusBadgeClassName } from '@/lib/trial-status';
import { index as approvalsIndex } from '@/routes/approvals';
import { edit as reviewEdit } from '@/routes/trials/review';
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

const DECISIONS = [
    { value: 'Approved', label: 'Approve', variant: 'default' as const },
    {
        value: 'Need Revision',
        label: 'Need Revision',
        variant: 'outline' as const,
    },
    { value: 'Rejected', label: 'Reject', variant: 'destructive' as const },
];

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
                                    <TableHead>Decision</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {items.data.map((item) => (
                                    <TableRow key={item.id}>
                                        <TableCell>
                                            <Link
                                                href={
                                                    reviewEdit({
                                                        trial: item.id,
                                                    }).url
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
                                            <div className="flex flex-wrap gap-2">
                                                {DECISIONS.map((decision) => (
                                                    <ConfirmDialog
                                                        key={decision.value}
                                                        trigger={
                                                            <Button
                                                                type="button"
                                                                size="sm"
                                                                variant={
                                                                    decision.variant
                                                                }
                                                            >
                                                                {decision.label}
                                                            </Button>
                                                        }
                                                        title={`${decision.label} — ${item.trial_code}`}
                                                        description="Masukkan comment dan password akun Anda sebagai e-signature untuk mengonfirmasi keputusan ini."
                                                        confirmLabel={
                                                            decision.label
                                                        }
                                                        confirmVariant={
                                                            decision.variant
                                                        }
                                                        formProps={ApprovalController.update.form(
                                                            item.id,
                                                        )}
                                                    >
                                                        {({ errors }) => (
                                                            <div className="space-y-3">
                                                                <input
                                                                    type="hidden"
                                                                    name="decision"
                                                                    value={
                                                                        decision.value
                                                                    }
                                                                />
                                                                <div className="grid gap-2">
                                                                    <Label
                                                                        htmlFor={`approval_comment_${item.id}_${decision.value}`}
                                                                    >
                                                                        Comment
                                                                    </Label>
                                                                    <Textarea
                                                                        id={`approval_comment_${item.id}_${decision.value}`}
                                                                        name="approval_comment"
                                                                        required
                                                                        placeholder="Comment approval..."
                                                                    />
                                                                    {errors.approval_comment && (
                                                                        <p className="text-sm text-destructive">
                                                                            {
                                                                                errors.approval_comment
                                                                            }
                                                                        </p>
                                                                    )}
                                                                </div>
                                                                <div className="grid gap-2">
                                                                    <Label
                                                                        htmlFor={`signature_password_${item.id}_${decision.value}`}
                                                                    >
                                                                        Password
                                                                        e-signature
                                                                    </Label>
                                                                    <Input
                                                                        id={`signature_password_${item.id}_${decision.value}`}
                                                                        type="password"
                                                                        name="signature_password"
                                                                        required
                                                                        autoComplete="current-password"
                                                                    />
                                                                    {errors.signature_password && (
                                                                        <p className="text-sm text-destructive">
                                                                            {
                                                                                errors.signature_password
                                                                            }
                                                                        </p>
                                                                    )}
                                                                </div>
                                                                {errors.decision && (
                                                                    <p className="text-sm text-destructive">
                                                                        {
                                                                            errors.decision
                                                                        }
                                                                    </p>
                                                                )}
                                                            </div>
                                                        )}
                                                    </ConfirmDialog>
                                                ))}
                                            </div>
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
