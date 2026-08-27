import { Form, Head, Link } from '@inertiajs/react';
import { useEffect, useMemo, useRef, useState } from 'react';
import type { ChangeEvent } from 'react';
import TrialAttachmentController from '@/actions/App/Http/Controllers/TrialAttachmentController';
import { Combobox } from '@/components/combobox';
import { ConfirmDialog } from '@/components/confirm-dialog';
import Heading from '@/components/heading';
import { TrialWizardSteps } from '@/components/trial-wizard-steps';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { handleAttachmentImageError } from '@/lib/image-fallback';
import { trialStatusBadgeClassName } from '@/lib/trial-status';
import { dashboard } from '@/routes';
import { edit as reviewEdit } from '@/routes/trials/review';
import weighing from '@/routes/trials/weighing';

type TrialData = {
    id: number;
    trial_code: string;
    current_step: string | null;
    progress_status: string;
    final_decision: string | null;
    product_type: string;
};

type AttachmentFile = {
    id: number;
    category: string;
    file_name: string;
    url: string;
};

type PageProps = {
    trial: TrialData;
    categories: string[];
    files: AttachmentFile[];
    canEdit: boolean;
};

const ACCEPTED_TYPES = 'image/jpeg,image/png,image/webp,image/gif';

function groupByCategory(
    files: AttachmentFile[],
): [string, AttachmentFile[]][] {
    const groups = new Map<string, AttachmentFile[]>();

    for (const file of files) {
        const bucket = groups.get(file.category) ?? [];
        bucket.push(file);
        groups.set(file.category, bucket);
    }

    return Array.from(groups.entries());
}

function UploadPreview({
    files,
    onRemove,
}: {
    files: File[];
    onRemove: (index: number) => void;
}) {
    const urls = useMemo(
        () => files.map((f) => URL.createObjectURL(f)),
        [files],
    );

    useEffect(() => {
        return () => urls.forEach((url) => URL.revokeObjectURL(url));
    }, [urls]);

    if (files.length === 0) {
        return null;
    }

    return (
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-4 md:grid-cols-6">
            {files.map((file, index) => (
                <figure key={index} className="space-y-1 rounded-md border p-2">
                    <img
                        src={urls[index]}
                        alt={file.name}
                        className="aspect-square w-full rounded object-cover"
                    />
                    <figcaption className="truncate text-xs text-muted-foreground">
                        {file.name}
                    </figcaption>
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        className="w-full text-destructive"
                        onClick={() => onRemove(index)}
                    >
                        Remove
                    </Button>
                </figure>
            ))}
        </div>
    );
}

export default function TrialAttachments({
    trial,
    categories,
    files,
    canEdit,
}: PageProps) {
    const inputRef = useRef<HTMLInputElement>(null);
    const [selected, setSelected] = useState<File[]>([]);
    const [category, setCategory] = useState(categories[0] ?? '');
    const categoryOptions = categories.map((c) => ({ value: c, label: c }));

    function syncInput(next: File[]) {
        if (!inputRef.current) {
            return;
        }

        const dt = new DataTransfer();
        next.forEach((file) => dt.items.add(file));
        inputRef.current.files = dt.files;
    }

    function handleFilesChange(e: ChangeEvent<HTMLInputElement>) {
        setSelected(Array.from(e.target.files ?? []));
    }

    function removeSelected(index: number) {
        const next = selected.filter((_, i) => i !== index);
        setSelected(next);
        syncInput(next);
    }

    const backHref = weighing.edit({ trial: trial.id, section: 'Filling' }).url;
    const grouped = groupByCategory(files);

    return (
        <>
            <Head title={`Attachments — ${trial.trial_code}`} />

            <div className="mx-auto max-w-6xl space-y-6 p-4">
                <div className="flex items-center justify-between gap-4">
                    <Heading
                        title="Attachments"
                        description={`Upload dan kelola evidence foto trial ${trial.trial_code}.`}
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

                <TrialWizardSteps currentStep={5} trial={trial} />

                {canEdit ? (
                    <Form
                        {...TrialAttachmentController.store.form(trial.id)}
                        options={{ preserveScroll: true }}
                        onSuccess={() => {
                            setSelected([]);
                            syncInput([]);
                            setCategory(categories[0] ?? '');
                        }}
                        className="space-y-4"
                    >
                        {({ processing, errors }) => (
                            <Card>
                                <CardHeader>
                                    <CardTitle>Upload Foto</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    {errors.category && (
                                        <Alert variant="destructive">
                                            <AlertDescription>
                                                {errors.category}
                                            </AlertDescription>
                                        </Alert>
                                    )}
                                    {errors.photos && (
                                        <Alert variant="destructive">
                                            <AlertDescription>
                                                {errors.photos}
                                            </AlertDescription>
                                        </Alert>
                                    )}

                                    <div className="grid gap-2">
                                        <Label htmlFor="category">
                                            Category
                                        </Label>
                                        <Combobox
                                            options={categoryOptions}
                                            value={category}
                                            onChange={setCategory}
                                            placeholder="Pilih category..."
                                            searchPlaceholder="Cari category..."
                                            disabled={categories.length === 0}
                                            className="w-full sm:w-2/3"
                                        />
                                        <input
                                            type="hidden"
                                            name="category"
                                            value={category}
                                        />
                                    </div>
                                    <div className="grid gap-2">
                                        <Label htmlFor="photos">Photos</Label>
                                        <Input
                                            id="photos"
                                            ref={inputRef}
                                            type="file"
                                            name="photos[]"
                                            multiple
                                            accept={ACCEPTED_TYPES}
                                            onChange={handleFilesChange}
                                        />
                                    </div>

                                    <UploadPreview
                                        files={selected}
                                        onRemove={removeSelected}
                                    />

                                    <div className="flex justify-end">
                                        <Button
                                            type="submit"
                                            disabled={
                                                processing ||
                                                categories.length === 0
                                            }
                                        >
                                            Upload
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        )}
                    </Form>
                ) : (
                    <Alert>
                        <AlertDescription>
                            Attachment readonly. Foto hanya bisa dihapus saat
                            status Draft atau Need Revision.
                        </AlertDescription>
                    </Alert>
                )}

                {grouped.map(([category, categoryFiles]) => (
                    <Card key={category}>
                        <CardHeader className="flex flex-row items-center justify-between space-y-0">
                            <CardTitle>{category}</CardTitle>
                            <span className="text-sm text-muted-foreground">
                                {categoryFiles.length} foto
                            </span>
                        </CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-2 gap-3 sm:grid-cols-4 md:grid-cols-6">
                                {categoryFiles.map((file) => (
                                    <figure
                                        key={file.id}
                                        className="space-y-1 rounded-md border p-2"
                                    >
                                        <img
                                            src={file.url}
                                            alt={file.file_name}
                                            onError={handleAttachmentImageError}
                                            className="aspect-square w-full rounded object-cover"
                                        />
                                        <figcaption className="truncate text-xs text-muted-foreground">
                                            {file.file_name}
                                        </figcaption>
                                        {canEdit && (
                                            <ConfirmDialog
                                                trigger={
                                                    <Button
                                                        type="button"
                                                        variant="ghost"
                                                        size="sm"
                                                        className="w-full text-destructive"
                                                    >
                                                        Delete
                                                    </Button>
                                                }
                                                title="Remove this photo?"
                                                description={file.file_name}
                                                confirmLabel="Delete"
                                                formProps={TrialAttachmentController.destroy.form(
                                                    {
                                                        trial: trial.id,
                                                        attachment: file.id,
                                                    },
                                                )}
                                            />
                                        )}
                                    </figure>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                ))}

                {files.length === 0 && (
                    <Card>
                        <CardContent className="py-6 text-center text-muted-foreground">
                            Belum ada attachment.
                        </CardContent>
                    </Card>
                )}

                <div className="flex justify-between">
                    <Button type="button" variant="secondary" asChild>
                        <Link href={backHref}>Back</Link>
                    </Button>
                    <Button type="button" asChild>
                        <Link href={reviewEdit({ trial: trial.id }).url}>
                            Continue to Review
                        </Link>
                    </Button>
                </div>
            </div>
        </>
    );
}

TrialAttachments.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Attachments', href: '#' },
    ],
};
