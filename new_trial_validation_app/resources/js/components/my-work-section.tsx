import { Link, usePage } from '@inertiajs/react';
import { ClipboardCheck, CircleCheckBig, FileEdit } from 'lucide-react';
import TrialReportController from '@/actions/App/Http/Controllers/TrialReportController';
import { TrialStepProgress } from '@/components/trial-step-progress';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { trialStatusBadgeClassName } from '@/lib/trial-status';
import { index as approvalsIndex } from '@/routes/approvals';
import { index as reviewsIndex } from '@/routes/reviews';

type MyTrial = {
    id: number;
    trial_code: string;
    product_name: string;
    progress_status: string;
    current_step: string | null;
    pending_with: string | null;
};

type PendingReview = {
    id: number;
    trial_id: number;
    trial_code: string;
    product_name: string;
    department: string;
};

type RecentlyReviewed = {
    id: number;
    trial_id: number;
    trial_code: string | null;
    product_name: string | null;
    reviewed_at: string | null;
};

type PendingApproval = {
    id: number;
    trial_code: string;
    product_name: string;
};

type RecentlyDecided = {
    id: number;
    trial_code: string;
    product_name: string;
    final_decision: string | null;
};

export type MyWork = {
    myTrials: MyTrial[];
    myTrialsTotal: number;
    pendingReviews: PendingReview[];
    pendingReviewsTotal: number;
    recentlyReviewed: RecentlyReviewed[];
    pendingApprovals: PendingApproval[];
    pendingApprovalsTotal: number;
    recentlyDecided: RecentlyDecided[];
};

function trialLink(id: number, children: React.ReactNode) {
    return (
        <Link
            href={TrialReportController.show(id).url}
            className="font-medium underline underline-offset-2"
        >
            {children}
        </Link>
    );
}

export function MyWorkSection({ myWork }: { myWork: MyWork }) {
    const { canReviewTrials, canApproveTrials } = usePage<{
        canReviewTrials: boolean;
        canApproveTrials: boolean;
    }>().props;

    const showMyTrials = myWork.myTrialsTotal > 0;
    const showReviews = canReviewTrials;
    const showApprovals = canApproveTrials;

    if (!showMyTrials && !showReviews && !showApprovals) {
        return null;
    }

    return (
        <section className="space-y-3">
            <h2 className="text-lg font-semibold">My Work</h2>
            <div className="grid gap-4 lg:grid-cols-3">
                {showMyTrials && (
                    <Card>
                        <CardHeader className="flex-row items-center gap-2 space-y-0">
                            <FileEdit className="size-4 text-muted-foreground" />
                            <CardTitle className="text-sm">
                                Trial Saya ({myWork.myTrialsTotal})
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            {myWork.myTrials.map((trial) => (
                                <div
                                    key={trial.id}
                                    className="space-y-1 border-b pb-2 last:border-b-0 last:pb-0"
                                >
                                    <div className="flex items-center justify-between gap-2">
                                        {trialLink(trial.id, trial.trial_code)}
                                        <Badge
                                            variant="outline"
                                            className={trialStatusBadgeClassName(
                                                trial.progress_status,
                                                null,
                                            )}
                                        >
                                            {trial.progress_status}
                                        </Badge>
                                    </div>
                                    <div className="text-sm text-muted-foreground">
                                        {trial.product_name}
                                    </div>
                                    <TrialStepProgress trial={trial} />
                                    {trial.pending_with && (
                                        <div className="text-xs text-muted-foreground">
                                            Menunggu: {trial.pending_with}
                                        </div>
                                    )}
                                </div>
                            ))}
                            {myWork.myTrialsTotal > myWork.myTrials.length && (
                                <p className="text-xs text-muted-foreground">
                                    +
                                    {myWork.myTrialsTotal -
                                        myWork.myTrials.length}{' '}
                                    trial lainnya.
                                </p>
                            )}
                        </CardContent>
                    </Card>
                )}

                {showReviews && (
                    <Card>
                        <CardHeader className="flex-row items-center gap-2 space-y-0">
                            <ClipboardCheck className="size-4 text-muted-foreground" />
                            <CardTitle className="text-sm">
                                Perlu Review Saya ({myWork.pendingReviewsTotal})
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            {myWork.pendingReviews.length === 0 ? (
                                <p className="text-sm text-muted-foreground">
                                    Tidak ada review yang menunggu Anda saat
                                    ini.
                                </p>
                            ) : (
                                myWork.pendingReviews.map((item) => (
                                    <div
                                        key={item.id}
                                        className="space-y-1 border-b pb-2 last:border-b-0 last:pb-0"
                                    >
                                        <div className="flex items-center justify-between gap-2">
                                            {trialLink(
                                                item.trial_id,
                                                item.trial_code,
                                            )}
                                            <span className="text-xs text-muted-foreground">
                                                {item.department}
                                            </span>
                                        </div>
                                        <div className="text-sm text-muted-foreground">
                                            {item.product_name}
                                        </div>
                                    </div>
                                ))
                            )}
                            <div className="flex items-center justify-between gap-2 pt-1">
                                <Link
                                    href={reviewsIndex().url}
                                    className="text-xs underline underline-offset-2"
                                >
                                    Lihat semua Review Queue
                                </Link>
                                {myWork.recentlyReviewed.length > 0 && (
                                    <span className="text-xs text-muted-foreground">
                                        Terakhir:{' '}
                                        {myWork.recentlyReviewed[0]
                                            .trial_code ?? '-'}
                                    </span>
                                )}
                            </div>
                        </CardContent>
                    </Card>
                )}

                {showApprovals && (
                    <Card>
                        <CardHeader className="flex-row items-center gap-2 space-y-0">
                            <CircleCheckBig className="size-4 text-muted-foreground" />
                            <CardTitle className="text-sm">
                                Perlu Approval Saya (
                                {myWork.pendingApprovalsTotal})
                            </CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-3">
                            {myWork.pendingApprovals.length === 0 ? (
                                <p className="text-sm text-muted-foreground">
                                    Tidak ada trial yang menunggu approval Anda
                                    saat ini.
                                </p>
                            ) : (
                                myWork.pendingApprovals.map((item) => (
                                    <div
                                        key={item.id}
                                        className="space-y-1 border-b pb-2 last:border-b-0 last:pb-0"
                                    >
                                        {trialLink(item.id, item.trial_code)}
                                        <div className="text-sm text-muted-foreground">
                                            {item.product_name}
                                        </div>
                                    </div>
                                ))
                            )}
                            <div className="flex items-center justify-between gap-2 pt-1">
                                <Link
                                    href={approvalsIndex().url}
                                    className="text-xs underline underline-offset-2"
                                >
                                    Lihat semua Approval Queue
                                </Link>
                                {myWork.recentlyDecided.length > 0 && (
                                    <span className="text-xs text-muted-foreground">
                                        Terakhir:{' '}
                                        {myWork.recentlyDecided[0].trial_code} (
                                        {
                                            myWork.recentlyDecided[0]
                                                .final_decision
                                        }
                                        )
                                    </span>
                                )}
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
        </section>
    );
}
