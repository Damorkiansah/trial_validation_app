// Port of status_class()/status_badge() in the legacy app's app/bootstrap.php.
export const TRIAL_STATUSES = [
    'Draft',
    'In Review',
    'Ready for Approval',
    'Approved',
    'Need Revision',
    'Rejected',
] as const;

export function trialStatusBadgeClassName(
    status: string,
    finalDecision: string | null,
) {
    if (status === 'Draft') {
        return 'bg-muted text-muted-foreground';
    }

    if (status === 'In Review') {
        return 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-200';
    }

    if (status === 'Ready for Approval' || status === 'Need Revision') {
        return 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200';
    }

    if (status === 'Approved') {
        return 'bg-green-100 text-green-800 dark:bg-green-950 dark:text-green-200';
    }

    if (status === 'Rejected' || finalDecision === 'Rejected') {
        return 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-200';
    }

    return 'bg-muted text-muted-foreground';
}
