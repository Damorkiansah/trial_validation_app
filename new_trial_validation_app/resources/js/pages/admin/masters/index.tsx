import { Form, Head } from '@inertiajs/react';
import { useState } from 'react';
import MasterOptionController from '@/actions/App/Http/Controllers/Admin/MasterOptionController';
import { ConfirmDialog } from '@/components/confirm-dialog';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { PaginationFooter } from '@/components/pagination-footer';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
import { index as mastersIndex } from '@/routes/admin/masters';
import type { Paginated } from '@/types';

type MasterOption = {
    id: number;
    type: string;
    name: string;
    sort_order: number;
};

type PageProps = {
    options: Paginated<MasterOption>;
    types: string[];
};

function MasterOptionFormDialog({
    open,
    onOpenChange,
    editingOption,
    types,
}: {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    editingOption: MasterOption | null;
    types: string[];
}) {
    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>
                        {editingOption ? 'Edit Master Option' : 'Add Master'}
                    </DialogTitle>
                </DialogHeader>
                <Form
                    {...MasterOptionController.store.form()}
                    options={{ preserveScroll: true }}
                    onSuccess={() => onOpenChange(false)}
                    resetOnSuccess={['type', 'name', 'sort_order']}
                    className="grid gap-4"
                >
                    {({ processing, errors }) => (
                        <>
                            {editingOption && (
                                <input
                                    type="hidden"
                                    name="id"
                                    value={editingOption.id}
                                />
                            )}

                            <div className="grid gap-2">
                                <Label htmlFor="type">Type</Label>
                                <Select
                                    name="type"
                                    defaultValue={
                                        editingOption?.type ?? types[0]
                                    }
                                >
                                    <SelectTrigger id="type">
                                        <SelectValue placeholder="Pilih type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {types.map((type) => (
                                            <SelectItem key={type} value={type}>
                                                {type}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.type} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="name">Name</Label>
                                <Input
                                    id="name"
                                    name="name"
                                    required
                                    defaultValue={editingOption?.name ?? ''}
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="sort_order">Sort</Label>
                                <Input
                                    id="sort_order"
                                    name="sort_order"
                                    type="number"
                                    defaultValue={
                                        editingOption?.sort_order ?? 0
                                    }
                                />
                                <InputError message={errors.sort_order} />
                            </div>

                            <DialogFooter>
                                <Button type="submit" disabled={processing}>
                                    {editingOption
                                        ? 'Save Changes'
                                        : 'Add Master'}
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}

export default function AdminMastersIndex({ options, types }: PageProps) {
    const [dialogOpen, setDialogOpen] = useState(false);
    const [editingOption, setEditingOption] = useState<MasterOption | null>(
        null,
    );

    function openCreate() {
        setEditingOption(null);
        setDialogOpen(true);
    }

    function openEdit(option: MasterOption) {
        setEditingOption(option);
        setDialogOpen(true);
    }

    return (
        <>
            <Head title="Masters" />

            <div className="space-y-6 p-4">
                <Heading
                    title="Master Template"
                    description="Kelola pilihan dropdown untuk trial validation."
                />

                <MasterOptionFormDialog
                    open={dialogOpen}
                    onOpenChange={setDialogOpen}
                    editingOption={editingOption}
                    types={types}
                />

                <Card>
                    <CardHeader className="flex-row items-center justify-end">
                        <Button type="button" onClick={openCreate}>
                            Add Master
                        </Button>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Type</TableHead>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Sort</TableHead>
                                    <TableHead>Action</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {options.data.map((option) => (
                                    <TableRow key={option.id}>
                                        <TableCell>{option.type}</TableCell>
                                        <TableCell>{option.name}</TableCell>
                                        <TableCell>
                                            {option.sort_order}
                                        </TableCell>
                                        <TableCell>
                                            <div className="flex gap-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() =>
                                                        openEdit(option)
                                                    }
                                                >
                                                    Edit
                                                </Button>
                                                <ConfirmDialog
                                                    trigger={
                                                        <Button
                                                            variant="destructive"
                                                            size="sm"
                                                        >
                                                            Delete
                                                        </Button>
                                                    }
                                                    title="Delete this master option?"
                                                    description={`${option.type} — ${option.name} will be removed from the dropdown list. Data lama yang sudah memakai nilai ini tidak ikut terhapus.`}
                                                    confirmLabel="Delete"
                                                    formProps={MasterOptionController.destroy.form(
                                                        option.id,
                                                    )}
                                                />
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {options.data.length === 0 && (
                                    <TableRow>
                                        <TableCell
                                            colSpan={4}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada master option aktif.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        <PaginationFooter
                            url={mastersIndex().url}
                            currentPage={options.current_page}
                            lastPage={options.last_page}
                            total={options.total}
                            itemLabel="options"
                        />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

AdminMastersIndex.layout = {
    breadcrumbs: [{ title: 'Masters', href: mastersIndex() }],
};
