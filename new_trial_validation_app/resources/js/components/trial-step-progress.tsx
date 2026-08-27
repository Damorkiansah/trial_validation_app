import {
    resolveTrialCompletedSteps,
    TRIAL_WIZARD_STEPS,
} from '@/lib/trial-wizard';
import type { TrialWizardTrial } from '@/lib/trial-wizard';

/**
 * Compact wizard-progress bar for a trial, for use outside the wizard shell
 * itself (trials-table.tsx's "Current Step" column, the per-trial report
 * page, etc). Only meaningful while a trial is still Draft — once it leaves
 * Draft it has, by definition, already cleared all 6 Staff-facing wizard
 * steps, so this renders as "selesai" rather than a step count.
 */
export function TrialStepProgress({ trial }: { trial: TrialWizardTrial }) {
    const completed = resolveTrialCompletedSteps(trial);

    if (completed === null) {
        return null;
    }

    const total = TRIAL_WIZARD_STEPS.length;
    const pct = Math.round((completed / total) * 100);
    const label =
        trial.progress_status !== 'Draft'
            ? 'Wizard selesai'
            : `Step ${completed} dari ${total}`;

    return (
        <div className="w-28 space-y-1">
            <div className="h-1.5 w-full overflow-hidden rounded-full bg-muted">
                <div
                    className="h-full rounded-full bg-brand"
                    style={{ width: `${pct}%` }}
                />
            </div>
            <div className="text-xs text-muted-foreground">{label}</div>
        </div>
    );
}
