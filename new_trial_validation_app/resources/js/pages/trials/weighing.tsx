import { Form, Head, Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import TrialWeighingController from '@/actions/App/Http/Controllers/TrialWeighingController';
import Heading from '@/components/heading';
import { TrialWizardSteps } from '@/components/trial-wizard-steps';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { trialStatusBadgeClassName } from '@/lib/trial-status';
import { dashboard } from '@/routes';
import validation from '@/routes/trials/validation';
import weighing from '@/routes/trials/weighing';

type Section = 'Packaging' | 'Filling';

type TrialData = {
    id: number;
    trial_code: string;
    current_step: string | null;
    progress_status: string;
    final_decision: string | null;
    product_type: string;
};

type PageProps = {
    trial: TrialData;
    section: Section;
    values: Record<string, string | null>;
    skip: boolean;
    canEdit: boolean;
};

// Port of legacy's section_display_name() in app/bootstrap.php.
const SECTION_LABELS: Record<Section, string> = {
    Packaging: 'Empty packaging (gr)',
    Filling: 'Filling Weight (gr)',
};

const WIZARD_STEP: Record<Section, number> = {
    Packaging: 3,
    Filling: 4,
};

function parseNumericValues(
    values: Record<number, string>,
    maxItemNo: number,
): number[] {
    const parsed: number[] = [];

    for (let i = 1; i <= maxItemNo; i++) {
        const raw = values[i];

        if (raw === undefined || raw.trim() === '') {
            continue;
        }

        const n = parseFloat(raw);

        if (!Number.isNaN(n)) {
            parsed.push(n);
        }
    }

    return parsed;
}

export default function TrialWeighing({
    trial,
    section,
    values,
    skip: initialSkip,
    canEdit,
}: PageProps) {
    const initialMaxItemNo = useMemo(() => {
        const keys = Object.keys(values).map((k) => parseInt(k, 10));

        return keys.length ? Math.max(30, ...keys) : 30;
    }, [values]);

    const [skip, setSkip] = useState(initialSkip);
    const [maxItemNo, setMaxItemNo] = useState(initialMaxItemNo);
    const [sampleValues, setSampleValues] = useState<Record<number, string>>(
        () => {
            const initial: Record<number, string> = {};

            for (const [itemNo, value] of Object.entries(values)) {
                initial[parseInt(itemNo, 10)] = value ?? '';
            }

            return initial;
        },
    );

    const stats = useMemo(() => {
        const nums = parseNumericValues(sampleValues, maxItemNo);

        return {
            total: nums.length,
            average: nums.length
                ? (nums.reduce((a, b) => a + b, 0) / nums.length).toFixed(2)
                : '-',
            min: nums.length ? Math.min(...nums).toFixed(2) : '-',
            max: nums.length ? Math.max(...nums).toFixed(2) : '-',
        };
    }, [sampleValues, maxItemNo]);

    const backHref =
        section === 'Packaging'
            ? validation.edit(trial.id).url
            : weighing.edit({ trial: trial.id, section: 'Packaging' }).url;

    const hasAnyValue = stats.total > 0;
    const showMissingSampleError = canEdit && !skip && !hasAnyValue;

    const grid = (
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-4 md:grid-cols-6">
            {Array.from({ length: maxItemNo }, (_, i) => i + 1).map(
                (itemNo) => (
                    <Label
                        key={itemNo}
                        className="flex flex-col gap-1 text-xs text-muted-foreground"
                    >
                        {itemNo}
                        <Input
                            type="number"
                            step="0.001"
                            min="0"
                            inputMode="decimal"
                            name={`w[${itemNo}]`}
                            value={sampleValues[itemNo] ?? ''}
                            onChange={(e) =>
                                setSampleValues((prev) => ({
                                    ...prev,
                                    [itemNo]: e.target.value,
                                }))
                            }
                            disabled={!canEdit || skip}
                        />
                    </Label>
                ),
            )}
        </div>
    );

    const statsBar = (
        <div className="flex flex-wrap gap-6 text-sm">
            <div>
                <span className="font-medium">Total Sample:</span> {stats.total}
            </div>
            <div>
                <span className="font-medium">Average:</span> {stats.average}
            </div>
            <div>
                <span className="font-medium">Minimum:</span> {stats.min}
            </div>
            <div>
                <span className="font-medium">Maximum:</span> {stats.max}
            </div>
        </div>
    );

    return (
        <>
            <Head
                title={`Weighing ${SECTION_LABELS[section]} — ${trial.trial_code}`}
            />

            <div className="mx-auto max-w-6xl space-y-6 p-4">
                <div className="flex items-center justify-between gap-4">
                    <Heading
                        title={`Weighing ${SECTION_LABELS[section]}`}
                        description={`Input hasil sampling untuk trial ${trial.trial_code}.`}
                    />
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

                <TrialWizardSteps
                    currentStep={WIZARD_STEP[section]}
                    trial={trial}
                />

                {canEdit ? (
                    <Form
                        {...TrialWeighingController.update.form({
                            trial: trial.id,
                            section,
                        })}
                        options={{ preserveScroll: true }}
                        className="space-y-6"
                    >
                        {({ processing, errors }) => (
                            <>
                                <Card>
                                    <CardHeader>
                                        <CardTitle>
                                            Weighing {SECTION_LABELS[section]}
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        {errors.w && (
                                            <Alert variant="destructive">
                                                <AlertDescription>
                                                    {errors.w}
                                                </AlertDescription>
                                            </Alert>
                                        )}
                                        {showMissingSampleError && (
                                            <Alert variant="destructive">
                                                <AlertDescription>
                                                    Please input at least 1
                                                    weighing sample or click
                                                    Skip.
                                                </AlertDescription>
                                            </Alert>
                                        )}

                                        <div className="flex items-center gap-2">
                                            <Checkbox
                                                id="skip"
                                                name="skip"
                                                checked={skip}
                                                onCheckedChange={(value) =>
                                                    setSkip(value === true)
                                                }
                                            />
                                            <Label htmlFor="skip">
                                                Skip {SECTION_LABELS[section]}{' '}
                                                (N/A)
                                            </Label>
                                        </div>

                                        {grid}

                                        <Button
                                            type="button"
                                            variant="secondary"
                                            size="sm"
                                            disabled={skip}
                                            onClick={() =>
                                                setMaxItemNo((n) => n + 1)
                                            }
                                        >
                                            Add Sample
                                        </Button>

                                        {statsBar}
                                    </CardContent>
                                </Card>

                                <div className="flex justify-end gap-2">
                                    <Button
                                        type="button"
                                        variant="secondary"
                                        asChild
                                    >
                                        <Link href={backHref}>Back</Link>
                                    </Button>
                                    <Button type="submit" disabled={processing}>
                                        Save & Next
                                    </Button>
                                </div>
                            </>
                        )}
                    </Form>
                ) : (
                    <>
                        <Card>
                            <CardHeader>
                                <CardTitle>
                                    Weighing {SECTION_LABELS[section]}
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="flex items-center gap-2">
                                    <Checkbox
                                        id="skip"
                                        checked={skip}
                                        disabled
                                    />
                                    <Label htmlFor="skip">
                                        Skip {SECTION_LABELS[section]} (N/A)
                                    </Label>
                                </div>
                                {grid}
                                {statsBar}
                            </CardContent>
                        </Card>

                        <div className="flex justify-end">
                            <Button type="button" variant="secondary" asChild>
                                <Link href={backHref}>Back</Link>
                            </Button>
                        </div>
                    </>
                )}
            </div>
        </>
    );
}

TrialWeighing.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Weighing', href: '#' },
    ],
};
