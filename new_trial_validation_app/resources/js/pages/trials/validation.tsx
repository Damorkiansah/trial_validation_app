import { Form, Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import TrialValidationController from '@/actions/App/Http/Controllers/TrialValidationController';
import Heading from '@/components/heading';
import { TrialWizardSteps } from '@/components/trial-wizard-steps';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
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
import { dashboard } from '@/routes';
import { edit as editTrial, index as trialsIndex } from '@/routes/trials';

type TrialData = {
    id: number;
    trial_code: string;
    current_step: string | null;
    progress_status: string;
    final_decision: string | null;
    product_type: string;
};

type Parameter = {
    id: number;
    parameter_name: string;
    specification: string | null;
};

type ResultRow = {
    parameter_id: number;
    result_value: string | null;
    decision: string | null;
    remark: string | null;
};

type PageProps = {
    trial: TrialData;
    parameters: Parameter[];
    results: Record<string, ResultRow>;
    canEdit: boolean;
};

const DECISIONS = ['OK', 'NOT OK', 'N/A'] as const;

// Port of legacy's group mapping in TrialController::GROUPS — used only for
// the non-editable-viewer Back-link fallback (legacy's own fallback is a
// /report page this app hasn't built yet).
function trialListGroupFor(trial: TrialData): string {
    if (trial.final_decision === 'Rejected') {
        return 'rejected';
    }

    switch (trial.progress_status) {
        case 'In Review':
            return 'in-review';
        case 'Ready for Approval':
            return 'waiting-approval';
        case 'Approved':
            return 'approved';
        case 'Need Revision':
            return 'need-revision';
        case 'Rejected':
            return 'rejected';
        default:
            return 'draft';
    }
}

function ValidationParameterRow({
    index,
    parameter,
    initial,
    canEdit,
}: {
    index: number;
    parameter: Parameter;
    initial: ResultRow | undefined;
    canEdit: boolean;
}) {
    const [decision, setDecision] = useState(initial?.decision ?? 'OK');
    const [result, setResult] = useState(initial?.result_value ?? 'Conform');
    const [remark, setRemark] = useState(initial?.remark ?? '');

    function handleDecisionChange(value: string) {
        setDecision(value);

        if (value === 'N/A') {
            setResult('N/A');
        } else if (value === 'OK' && result.trim() === '') {
            setResult('Conform');
        }
    }

    return (
        <TableRow
            className={decision === 'NOT OK' ? 'bg-destructive/10' : undefined}
        >
            <TableCell className="font-medium">
                {parameter.parameter_name}
                <input
                    type="hidden"
                    name={`results[${index}][parameter_id]`}
                    value={parameter.id}
                />
            </TableCell>
            <TableCell className="whitespace-pre-line text-muted-foreground">
                {parameter.specification}
            </TableCell>
            <TableCell>
                <Select
                    name={`results[${index}][decision]`}
                    value={decision}
                    onValueChange={handleDecisionChange}
                    disabled={!canEdit}
                >
                    <SelectTrigger className="w-28">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        {DECISIONS.map((d) => (
                            <SelectItem key={d} value={d}>
                                {d}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </TableCell>
            <TableCell>
                <Input
                    name={`results[${index}][result]`}
                    value={result}
                    onChange={(e) => setResult(e.target.value)}
                    disabled={!canEdit}
                    readOnly={!canEdit}
                />
            </TableCell>
            <TableCell>
                <Textarea
                    name={`results[${index}][remark]`}
                    value={remark}
                    onChange={(e) => setRemark(e.target.value)}
                    disabled={!canEdit}
                    readOnly={!canEdit}
                    rows={2}
                />
            </TableCell>
        </TableRow>
    );
}

export default function TrialValidation({
    trial,
    parameters,
    results,
    canEdit,
}: PageProps) {
    const backHref = canEdit
        ? editTrial(trial.id).url
        : trialsIndex(trialListGroupFor(trial)).url;

    const table = (
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead>Parameter</TableHead>
                    <TableHead>Specification</TableHead>
                    <TableHead>Decision</TableHead>
                    <TableHead>Result</TableHead>
                    <TableHead>Remark</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                {parameters.map((parameter, index) => (
                    <ValidationParameterRow
                        key={parameter.id}
                        index={index}
                        parameter={parameter}
                        initial={results[String(parameter.id)]}
                        canEdit={canEdit}
                    />
                ))}
            </TableBody>
        </Table>
    );

    return (
        <>
            <Head title={`Validation — ${trial.trial_code}`} />

            <div className="mx-auto max-w-6xl space-y-6 p-4">
                <div className="flex items-center justify-between gap-4">
                    <Heading
                        title={`Validation Trial Parameter — ${trial.product_type}`}
                        description="Isi Decision, Result, dan Remark untuk setiap parameter validasi."
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

                <TrialWizardSteps currentStep={2} trial={trial} />

                {parameters.length === 0 ? (
                    <>
                        <Alert>
                            <AlertDescription>
                                Parameter validation untuk product type ini
                                belum dikonfigurasi.
                            </AlertDescription>
                        </Alert>
                        <div className="flex justify-end">
                            <Button type="button" variant="secondary" asChild>
                                <Link href={backHref}>Back</Link>
                            </Button>
                        </div>
                    </>
                ) : canEdit ? (
                    <Form
                        {...TrialValidationController.update.form(trial.id)}
                        options={{ preserveScroll: true }}
                        className="space-y-6"
                    >
                        {({ processing, errors }) => (
                            <>
                                <Card>
                                    <CardHeader>
                                        <CardTitle>
                                            Parameter Validasi
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        {errors.results && (
                                            <Alert variant="destructive">
                                                <AlertDescription>
                                                    {errors.results}
                                                </AlertDescription>
                                            </Alert>
                                        )}
                                        {table}
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
                                <CardTitle>Parameter Validasi</CardTitle>
                            </CardHeader>
                            <CardContent>{table}</CardContent>
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

TrialValidation.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Validation', href: '#' },
    ],
};
