import { Check } from 'lucide-react';
import {
    resolveTrialCompletedSteps,
    TRIAL_WIZARD_STEPS
    
} from '@/lib/trial-wizard';
import type {TrialWizardTrial} from '@/lib/trial-wizard';
import { cn } from '@/lib/utils';

type StepState = 'complete' | 'current' | 'upcoming';

export function TrialWizardSteps({
    currentStep,
    trial,
}: {
    currentStep: number;
    trial?: TrialWizardTrial | null;
}) {
    const completedSteps = resolveTrialCompletedSteps(trial);
    const showProgressNote =
        completedSteps !== null && completedSteps > currentStep;

    return (
        <nav aria-label="Trial progress" className="mb-2">
            <p className="mb-3 text-sm font-medium text-muted-foreground">
                Step {currentStep} of {TRIAL_WIZARD_STEPS.length}
            </p>
            <ol className="flex flex-wrap items-start gap-x-1 gap-y-4">
                {TRIAL_WIZARD_STEPS.map((step, i) => {
                    const state: StepState =
                        step.number === currentStep
                            ? 'current'
                            : completedSteps !== null &&
                                step.number <= completedSteps
                              ? 'complete'
                              : 'upcoming';

                    return (
                        <li key={step.key} className="flex items-center">
                            {i > 0 && (
                                <span
                                    aria-hidden
                                    className="mx-2 h-px w-6 shrink-0 bg-border sm:w-10"
                                />
                            )}
                            <div
                                className={cn(
                                    'flex w-16 flex-col items-center gap-1 text-center',
                                    state === 'upcoming' && 'opacity-50',
                                )}
                            >
                                <span
                                    className={cn(
                                        'flex size-8 items-center justify-center rounded-full border text-sm font-medium',
                                        state === 'current' &&
                                            'border-brand bg-brand text-white',
                                        state === 'complete' &&
                                            'border-brand bg-brand/10 text-brand',
                                        state === 'upcoming' &&
                                            'border-border bg-muted text-muted-foreground',
                                    )}
                                >
                                    {state === 'complete' ? (
                                        <Check className="size-4" />
                                    ) : (
                                        step.number
                                    )}
                                </span>
                                <span className="text-xs leading-tight text-muted-foreground">
                                    {step.label}
                                </span>
                            </div>
                        </li>
                    );
                })}
            </ol>
            {showProgressNote && trial && completedSteps !== null && (
                <p className="mt-2 text-xs text-muted-foreground">
                    Trial ini sudah berjalan sampai tahap{' '}
                    <strong>
                        {TRIAL_WIZARD_STEPS[completedSteps - 1].label}
                    </strong>{' '}
                    di sistem lama; layar ini hanya mengedit data header.
                </p>
            )}
        </nav>
    );
}
