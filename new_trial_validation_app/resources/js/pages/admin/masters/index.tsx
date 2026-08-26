import { Form, Head, router } from '@inertiajs/react';
import MasterOptionController from '@/actions/App/Http/Controllers/Admin/MasterOptionController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
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
import { index as mastersIndex } from '@/routes/admin/masters';

type MasterOption = {
    id: number;
    type: string;
    name: string;
    sort_order: number;
};

type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
};

type PageProps = {
    options: Paginated<MasterOption>;
    editOption: MasterOption | null;
    types: string[];
};

function DeleteMasterOptionButton({ option }: { option: MasterOption }) {
    return (
        <Dialog>
            <DialogTrigger asChild>
                <Button variant="destructive" size="sm">
                    Delete
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>Delete this master option?</DialogTitle>
                <DialogDescription>
                    {option.type} — {option.name} will be removed from the
                    dropdown list. Data lama yang sudah memakai nilai ini tidak
                    ikut terhapus.
                </DialogDescription>
                <DialogFooter className="gap-2">
                    <DialogClose asChild>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Form
                        {...MasterOptionController.destroy.form(option.id)}
                        options={{ preserveScroll: true }}
                    >
                        {({ processing }) => (
                            <Button
                                type="submit"
                                variant="destructive"
                                disabled={processing}
                            >
                                Delete
                            </Button>
                        )}
                    </Form>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}

export default function AdminMastersIndex({
    options,
    editOption,
    types,
}: PageProps) {
    return (
        <>
            <Head title="Masters" />

            <div className="space-y-6 p-4">
                <Heading
                    title="Master Template"
                    description="Kelola pilihan dropdown untuk trial validation."
                />

                <Form
                    {...MasterOptionController.store.form()}
                    options={{ preserveScroll: true }}
                    resetOnSuccess={['type', 'name', 'sort_order']}
                    className="grid gap-4 rounded-lg border p-4 sm:grid-cols-2 lg:grid-cols-4"
                >
                    {({ processing, errors }) => (
                        <>
                            {editOption && (
                                <input
                                    type="hidden"
                                    name="id"
                                    value={editOption.id}
                                />
                            )}

                            <div className="grid gap-2">
                                <Label htmlFor="type">Type</Label>
                                <Select
                                    name="type"
                                    defaultValue={editOption?.type ?? types[0]}
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
                                    defaultValue={editOption?.name ?? ''}
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="sort_order">Sort</Label>
                                <Input
                                    id="sort_order"
                                    name="sort_order"
                                    type="number"
                                    defaultValue={editOption?.sort_order ?? 0}
                                />
                                <InputError message={errors.sort_order} />
                            </div>

                            <div className="flex items-end gap-2">
                                <Button type="submit" disabled={processing}>
                                    {editOption
                                        ? 'Update Master'
                                        : 'Add Master'}
                                </Button>
                                {editOption && (
                                    <Button
                                        type="button"
                                        variant="secondary"
                                        onClick={() =>
                                            router.get(mastersIndex().url)
                                        }
                                    >
                                        Cancel
                                    </Button>
                                )}
                            </div>
                        </>
                    )}
                </Form>

                <div className="space-y-4 rounded-lg border p-4">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b text-left">
                                    <th className="p-2">Type</th>
                                    <th className="p-2">Name</th>
                                    <th className="p-2">Sort</th>
                                    <th className="p-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {options.data.map((option) => (
                                    <tr key={option.id} className="border-b">
                                        <td className="p-2">{option.type}</td>
                                        <td className="p-2">{option.name}</td>
                                        <td className="p-2">
                                            {option.sort_order}
                                        </td>
                                        <td className="p-2">
                                            <div className="flex gap-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() =>
                                                        router.get(
                                                            mastersIndex().url,
                                                            {
                                                                edit: option.id,
                                                            },
                                                        )
                                                    }
                                                >
                                                    Edit
                                                </Button>
                                                <DeleteMasterOptionButton
                                                    option={option}
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                                {options.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={4}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada master option aktif.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    <div className="flex items-center justify-between text-sm text-muted-foreground">
                        <span>
                            Page {options.current_page} of {options.last_page} (
                            {options.total} options)
                        </span>
                        <div className="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={options.current_page <= 1}
                                onClick={() =>
                                    router.get(mastersIndex().url, {
                                        page: options.current_page - 1,
                                    })
                                }
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={
                                    options.current_page >= options.last_page
                                }
                                onClick={() =>
                                    router.get(mastersIndex().url, {
                                        page: options.current_page + 1,
                                    })
                                }
                            >
                                Next
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

AdminMastersIndex.layout = {
    breadcrumbs: [{ title: 'Masters', href: mastersIndex() }],
};
