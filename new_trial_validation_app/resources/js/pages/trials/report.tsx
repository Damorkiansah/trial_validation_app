import { Form, Head, Link } from '@inertiajs/react';
import ApprovalController from '@/actions/App/Http/Controllers/ApprovalController';
import ReviewController from '@/actions/App/Http/Controllers/ReviewController';
import TrialReportController from '@/actions/App/Http/Controllers/TrialReportController';
import { ConfirmDialog } from '@/components/confirm-dialog';
import Heading from '@/components/heading';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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
import { handleAttachmentImageError } from '@/lib/image-fallback';
import { trialStatusBadgeClassName } from '@/lib/trial-status';
import { dashboard } from '@/routes';
import { edit as attachmentsEdit } from '@/routes/trials/attachments';
import { edit as reviewEdit } from '@/routes/trials/review';

type TrialData = {
    id: number;
    trial_code: string;
    product_name: string;
    finish_good_code: string;
    validation_category: string;
    validation_scope: string[] | null;
    product_type: string;
    validation_date: string | null;
    risk_level: string;
    machine_used: string[] | null;
    created_by: string | null;
    progress_status: string;
    final_decision: string | null;
    current_step: string | null;
    batch_number: string | null;
    bulk_code: string | null;
    support_team: string | null;
    initiated_person_team: string | null;
    reason: string | null;
    bom: string | null;
    pending_with: string | null;
    revision_no: number;
    approval_comment: string | null;
    approved_at: string | null;
    rejected_at: string | null;
    approver: { id: number; name: string; email: string } | null;
};

type ResultItem = {
    parameter_name: string;
    specification: string | null;
    decision: string | null;
    result_value: string | null;
    remark: string | null;
};

type WeighingSection = {
    section: 'Packaging' | 'Filling';
    stats: {
        values: string[];
        count: number;
        min: number | null;
        max: number | null;
        avg: number | null;
    };
};

type AttachmentFile = {
    id: number;
    file_name: string;
    url: string;
};

type ReviewItem = {
    department: string;
    review_round: number;
    status: string;
    reviewer_name: string | null;
    reviewed_at: string | null;
    comment: string | null;
};

type PendingReview = {
    id: number;
    department: string;
};

type PageProps = {
    trial: TrialData;
    results: ResultItem[];
    weighingSections: WeighingSection[];
    attachments: Record<string, AttachmentFile[]>;
    reviews: ReviewItem[];
    approvedByName: string | null;
    rejectedByName: string | null;
    completeness: string[];
    canEdit: boolean;
    canApprove: boolean;
    pendingReviews: PendingReview[];
    approvalBlockedNote: string | null;
    reviewCompletedNote: string | null;
};

const APPROVAL_DECISIONS = [
    { value: 'Approved', label: 'Approve', variant: 'default' as const },
    {
        value: 'Need Revision',
        label: 'Need Revision',
        variant: 'outline' as const,
    },
    { value: 'Rejected', label: 'Reject', variant: 'destructive' as const },
];

const DECISION_LABEL: Record<string, { by: string; at: string }> = {
    Approved: { by: 'Approved By', at: 'Approved At' },
    'Need Revision': {
        by: 'Revision Requested By',
        at: 'Revision Requested At',
    },
    Rejected: { by: 'Rejected By', at: 'Rejected At' },
};

function formatNumber(value: number | null): string {
    return value === null ? '-' : value.toFixed(2);
}

export default function TrialReport({
    trial,
    results,
    weighingSections,
    attachments,
    reviews,
    approvedByName,
    rejectedByName,
    completeness,
    canEdit,
    canApprove,
    pendingReviews,
    approvalBlockedNote,
    reviewCompletedNote,
}: PageProps) {
    const managerDecision = trial.final_decision ?? trial.progress_status;
    const hasDecision =
        Boolean(trial.approval_comment) ||
        Boolean(approvedByName) ||
        Boolean(rejectedByName);
    const decisionBy =
        managerDecision === 'Approved' ? approvedByName : rejectedByName;
    const decisionAt =
        managerDecision === 'Approved' ? trial.approved_at : trial.rejected_at;
    const decisionLabel = DECISION_LABEL[managerDecision] ?? {
        by: 'Decision By',
        at: 'Decision At',
    };
    const displayDecision =
        trial.progress_status === 'Approved' ||
        trial.progress_status === 'Rejected'
            ? (trial.final_decision ?? trial.progress_status)
            : trial.progress_status;
    const approvalAuthority = approvedByName ?? rejectedByName ?? '-';

    function handlePrint() {
        const xsrfToken = document.cookie
            .split('; ')
            .find((row) => row.startsWith('XSRF-TOKEN='))
            ?.split('=')[1];

        fetch(TrialReportController.logPrint(trial.id).url, {
            method: 'POST',
            headers: xsrfToken
                ? { 'X-XSRF-TOKEN': decodeURIComponent(xsrfToken) }
                : {},
        }).catch(() => {});
        window.print();
    }

    return (
        <>
            <Head title={`Report — ${trial.trial_code}`} />

            <div className="mx-auto max-w-6xl space-y-6 p-4">
                <div className="flex items-center justify-between gap-4 print:hidden">
                    <Heading
                        title="Report Summary"
                        description="Trial validation summary dan attachment evidence."
                    />
                    <Button onClick={handlePrint}>Print</Button>
                </div>

                {approvalBlockedNote && (
                    <Alert className="print:hidden">
                        <AlertTitle>Belum giliran Anda</AlertTitle>
                        <AlertDescription>
                            {approvalBlockedNote}
                        </AlertDescription>
                    </Alert>
                )}

                {reviewCompletedNote && (
                    <Alert className="print:hidden">
                        <AlertTitle>Review Anda sudah selesai</AlertTitle>
                        <AlertDescription>
                            {reviewCompletedNote}
                        </AlertDescription>
                    </Alert>
                )}

                {canEdit && (
                    <div className="flex flex-wrap items-center gap-3 print:hidden">
                        <Button variant="secondary" asChild>
                            <Link
                                href={attachmentsEdit({ trial: trial.id }).url}
                            >
                                Edit / Back
                            </Link>
                        </Button>
                        {completeness.length > 0 ? (
                            <Alert variant="destructive" className="flex-1">
                                <AlertTitle>
                                    Belum siap submit review
                                </AlertTitle>
                                <AlertDescription>
                                    <ul className="list-inside list-disc">
                                        {completeness.map((item) => (
                                            <li key={item}>{item}</li>
                                        ))}
                                    </ul>
                                </AlertDescription>
                            </Alert>
                        ) : (
                            trial.progress_status === 'Draft' && (
                                <Button asChild>
                                    <Link
                                        href={
                                            reviewEdit({ trial: trial.id }).url
                                        }
                                    >
                                        Submit for Review
                                    </Link>
                                </Button>
                            )
                        )}
                    </div>
                )}

                <Card className="print:border-none print:shadow-none">
                    <CardContent className="space-y-6 pt-6">
                        <div className="flex flex-wrap items-center justify-between gap-2 border-b pb-4">
                            <div>
                                <h2 className="text-lg font-semibold">
                                    Trial Validation System Report
                                </h2>
                                <p className="text-sm text-muted-foreground">
                                    FR.QSE.074.04
                                </p>
                            </div>
                            <Badge
                                variant="outline"
                                className={trialStatusBadgeClassName(
                                    trial.progress_status,
                                    trial.final_decision,
                                )}
                            >
                                {trial.progress_status}
                            </Badge>
                        </div>

                        <div className="grid gap-4 sm:grid-cols-3">
                            {[
                                ['Trial ID', trial.trial_code],
                                ['Product Name', trial.product_name],
                                ['FG Code', trial.finish_good_code],
                                [
                                    'Validation Category',
                                    trial.validation_category,
                                ],
                                [
                                    'Validation Scope',
                                    (trial.validation_scope ?? []).join(', '),
                                ],
                                ['Product Type', trial.product_type],
                                [
                                    'Validation Date',
                                    trial.validation_date ?? '-',
                                ],
                                ['Risk Level', trial.risk_level],
                                [
                                    'Machine Used',
                                    (trial.machine_used ?? []).join(', '),
                                ],
                                ['Created By', trial.created_by ?? '-'],
                                ['Approval Status', displayDecision],
                                ['Approval Authority', approvalAuthority],
                            ].map(([label, value]) => (
                                <div
                                    key={label}
                                    className="rounded-md border p-3"
                                >
                                    <div className="text-xs tracking-wide text-muted-foreground uppercase">
                                        {label}
                                    </div>
                                    <div className="font-medium">
                                        {value || '-'}
                                    </div>
                                </div>
                            ))}
                        </div>

                        <div>
                            <h3 className="mb-2 text-base font-semibold">
                                Header Detail
                            </h3>
                            <Table>
                                <TableBody>
                                    <TableRow>
                                        <TableCell className="font-medium">
                                            Batch Number
                                        </TableCell>
                                        <TableCell>
                                            {trial.batch_number ?? '-'}
                                        </TableCell>
                                        <TableCell className="font-medium">
                                            Bulk Code
                                        </TableCell>
                                        <TableCell>
                                            {trial.bulk_code ?? '-'}
                                        </TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell className="font-medium">
                                            Support Team
                                        </TableCell>
                                        <TableCell>
                                            {trial.support_team ?? '-'}
                                        </TableCell>
                                        <TableCell className="font-medium">
                                            Initiated person/team
                                        </TableCell>
                                        <TableCell>
                                            {trial.initiated_person_team ?? '-'}
                                        </TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell className="font-medium">
                                            Reason
                                        </TableCell>
                                        <TableCell colSpan={3}>
                                            {trial.reason ?? '-'}
                                        </TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell className="font-medium">
                                            B.O.M
                                        </TableCell>
                                        <TableCell
                                            colSpan={3}
                                            className="whitespace-pre-line"
                                        >
                                            {trial.bom ?? '-'}
                                        </TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell className="font-medium">
                                            Status
                                        </TableCell>
                                        <TableCell>
                                            {trial.progress_status}
                                        </TableCell>
                                        <TableCell className="font-medium">
                                            Pending With
                                        </TableCell>
                                        <TableCell>
                                            {trial.pending_with ?? '-'}
                                        </TableCell>
                                    </TableRow>
                                    {trial.approver && (
                                        <TableRow>
                                            <TableCell className="font-medium">
                                                Selected Approver
                                            </TableCell>
                                            <TableCell colSpan={3}>
                                                {trial.approver.name ||
                                                    trial.approver.email}
                                            </TableCell>
                                        </TableRow>
                                    )}
                                    <TableRow>
                                        <TableCell className="font-medium">
                                            Revision No
                                        </TableCell>
                                        <TableCell>
                                            {trial.revision_no ?? 0}
                                        </TableCell>
                                        <TableCell className="font-medium">
                                            Final Decision
                                        </TableCell>
                                        <TableCell>
                                            {trial.final_decision ?? '-'}
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>

                        <div>
                            <h3 className="mb-2 text-base font-semibold">
                                Validation Parameter
                            </h3>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Parameter</TableHead>
                                        <TableHead>Spec</TableHead>
                                        <TableHead>Decision</TableHead>
                                        <TableHead>Result</TableHead>
                                        <TableHead>Remark</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {results.map((r, i) => (
                                        <TableRow
                                            key={i}
                                            className={
                                                r.decision === 'NOT OK'
                                                    ? 'bg-red-50 dark:bg-red-950/30'
                                                    : undefined
                                            }
                                        >
                                            <TableCell>
                                                {r.parameter_name}
                                            </TableCell>
                                            <TableCell className="whitespace-pre-line">
                                                {r.specification ?? '-'}
                                            </TableCell>
                                            <TableCell>
                                                {r.decision ?? '-'}
                                            </TableCell>
                                            <TableCell>
                                                {r.result_value ?? '-'}
                                            </TableCell>
                                            <TableCell>
                                                {r.remark ?? '-'}
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                    {results.length === 0 && (
                                        <TableRow>
                                            <TableCell
                                                colSpan={5}
                                                className="text-center text-muted-foreground"
                                            >
                                                Belum ada data validation.
                                            </TableCell>
                                        </TableRow>
                                    )}
                                </TableBody>
                            </Table>
                        </div>

                        <div className="space-y-4">
                            <h3 className="text-base font-semibold">
                                Weighing
                            </h3>
                            {weighingSections.map((section) => (
                                <Card key={section.section}>
                                    <CardHeader>
                                        <CardTitle className="text-sm">
                                            {section.section} Weighing
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent>
                                        {section.stats.count === 0 ? (
                                            <p className="font-medium">
                                                {section.section} Weighing: N/A
                                            </p>
                                        ) : (
                                            <>
                                                <div className="mb-3 flex flex-wrap gap-2 text-sm">
                                                    {section.stats.values.map(
                                                        (v, i) => (
                                                            <span
                                                                key={i}
                                                                className="rounded border px-2 py-0.5"
                                                            >
                                                                {v}
                                                            </span>
                                                        ),
                                                    )}
                                                </div>
                                                <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                                    <div>
                                                        <div className="text-xs text-muted-foreground">
                                                            Total Sample
                                                        </div>
                                                        <div className="font-medium">
                                                            {
                                                                section.stats
                                                                    .count
                                                            }
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div className="text-xs text-muted-foreground">
                                                            Average
                                                        </div>
                                                        <div className="font-medium">
                                                            {formatNumber(
                                                                section.stats
                                                                    .avg,
                                                            )}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div className="text-xs text-muted-foreground">
                                                            Minimum
                                                        </div>
                                                        <div className="font-medium">
                                                            {formatNumber(
                                                                section.stats
                                                                    .min,
                                                            )}
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <div className="text-xs text-muted-foreground">
                                                            Maximum
                                                        </div>
                                                        <div className="font-medium">
                                                            {formatNumber(
                                                                section.stats
                                                                    .max,
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>
                                            </>
                                        )}
                                    </CardContent>
                                </Card>
                            ))}
                        </div>

                        <div>
                            <h3 className="mb-2 text-base font-semibold">
                                Attachment Summary
                            </h3>
                            {Object.keys(attachments).length === 0 ? (
                                <p className="text-muted-foreground">
                                    Tidak ada attachment.
                                </p>
                            ) : (
                                <div className="space-y-4">
                                    {Object.entries(attachments).map(
                                        ([category, files]) => (
                                            <div key={category}>
                                                <h4 className="mb-2 text-sm font-semibold">
                                                    {category}
                                                </h4>
                                                <div className="grid grid-cols-2 gap-3 sm:grid-cols-4 md:grid-cols-6">
                                                    {files.map((file) => (
                                                        <figure
                                                            key={file.id}
                                                            className="space-y-1 rounded-md border p-2"
                                                        >
                                                            <img
                                                                src={file.url}
                                                                alt={
                                                                    file.file_name
                                                                }
                                                                onError={
                                                                    handleAttachmentImageError
                                                                }
                                                                className="aspect-square w-full rounded object-cover"
                                                            />
                                                            <figcaption className="truncate text-xs text-muted-foreground">
                                                                {file.file_name}
                                                            </figcaption>
                                                        </figure>
                                                    ))}
                                                </div>
                                            </div>
                                        ),
                                    )}
                                </div>
                            )}
                        </div>

                        <div>
                            <h3 className="mb-2 text-base font-semibold">
                                Department Review
                            </h3>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Round</TableHead>
                                        <TableHead>Dept</TableHead>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Reviewer Name</TableHead>
                                        <TableHead>Reviewed At</TableHead>
                                        <TableHead>Comment</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {reviews.map((r) => (
                                        <TableRow key={r.department}>
                                            <TableCell>
                                                {r.review_round}
                                            </TableCell>
                                            <TableCell>
                                                {r.department}
                                            </TableCell>
                                            <TableCell>{r.status}</TableCell>
                                            <TableCell>
                                                {r.reviewer_name ?? '-'}
                                            </TableCell>
                                            <TableCell>
                                                {r.reviewed_at ?? '-'}
                                            </TableCell>
                                            <TableCell>
                                                {r.comment ?? '-'}
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </div>

                        {pendingReviews.length > 0 && (
                            <div className="print:hidden">
                                <h3 className="mb-2 text-base font-semibold">
                                    Review Department Anda
                                </h3>
                                <div className="space-y-4">
                                    {pendingReviews.map((pending) => (
                                        <Form
                                            key={pending.id}
                                            {...ReviewController.update.form(
                                                pending.id,
                                            )}
                                        >
                                            {({ processing, errors }) => (
                                                <Card>
                                                    <CardHeader>
                                                        <CardTitle className="text-sm">
                                                            Submit review untuk
                                                            department{' '}
                                                            {pending.department}
                                                        </CardTitle>
                                                    </CardHeader>
                                                    <CardContent className="space-y-3">
                                                        {errors.comment && (
                                                            <Alert variant="destructive">
                                                                <AlertDescription>
                                                                    {
                                                                        errors.comment
                                                                    }
                                                                </AlertDescription>
                                                            </Alert>
                                                        )}
                                                        <Textarea
                                                            name="comment"
                                                            required
                                                            placeholder="Comment review..."
                                                        />
                                                        <div className="flex justify-end">
                                                            <Button
                                                                type="submit"
                                                                disabled={
                                                                    processing
                                                                }
                                                            >
                                                                Submit Review
                                                            </Button>
                                                        </div>
                                                    </CardContent>
                                                </Card>
                                            )}
                                        </Form>
                                    ))}
                                </div>
                            </div>
                        )}

                        {canApprove && (
                            <div className="print:hidden">
                                <h3 className="mb-2 text-base font-semibold">
                                    Keputusan Approval
                                </h3>
                                <Card>
                                    <CardContent className="flex flex-wrap gap-2 pt-6">
                                        {APPROVAL_DECISIONS.map((decision) => (
                                            <ConfirmDialog
                                                key={decision.value}
                                                trigger={
                                                    <Button
                                                        type="button"
                                                        variant={
                                                            decision.variant
                                                        }
                                                    >
                                                        {decision.label}
                                                    </Button>
                                                }
                                                title={`${decision.label} — ${trial.trial_code}`}
                                                description="Masukkan comment dan password akun Anda sebagai e-signature untuk mengonfirmasi keputusan ini."
                                                confirmLabel={decision.label}
                                                confirmVariant={
                                                    decision.variant
                                                }
                                                formProps={ApprovalController.update.form(
                                                    trial.id,
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
                                                                htmlFor={`approval_comment_${decision.value}`}
                                                            >
                                                                Comment
                                                            </Label>
                                                            <Textarea
                                                                id={`approval_comment_${decision.value}`}
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
                                                                htmlFor={`signature_password_${decision.value}`}
                                                            >
                                                                Password
                                                                e-signature
                                                            </Label>
                                                            <Input
                                                                id={`signature_password_${decision.value}`}
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
                                    </CardContent>
                                </Card>
                            </div>
                        )}

                        {hasDecision && (
                            <div>
                                <h3 className="mb-2 text-base font-semibold">
                                    Manager QAC Decision
                                </h3>
                                <Table>
                                    <TableBody>
                                        <TableRow>
                                            <TableCell className="font-medium">
                                                Decision
                                            </TableCell>
                                            <TableCell>
                                                {managerDecision}
                                            </TableCell>
                                            <TableCell className="font-medium">
                                                Status
                                            </TableCell>
                                            <TableCell>
                                                {trial.progress_status}
                                            </TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">
                                                {decisionLabel.by}
                                            </TableCell>
                                            <TableCell>
                                                {decisionBy ?? '-'}
                                            </TableCell>
                                            <TableCell className="font-medium">
                                                {decisionLabel.at}
                                            </TableCell>
                                            <TableCell>
                                                {decisionAt ?? '-'}
                                            </TableCell>
                                        </TableRow>
                                        <TableRow>
                                            <TableCell className="font-medium">
                                                Comment
                                            </TableCell>
                                            <TableCell colSpan={3}>
                                                {trial.approval_comment ?? '-'}
                                            </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

TrialReport.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Report', href: '#' },
    ],
};
