import { Form, Head, router } from '@inertiajs/react';
import ParameterController from '@/actions/App/Http/Controllers/Admin/ParameterController';
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
import { Textarea } from '@/components/ui/textarea';
import { index as parametersIndex } from '@/routes/admin/parameters';

type Parameter = {
    id: number;
    product_type: string;
    parameter_name: string;
    specification: string | null;
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
    parameters: Paginated<Parameter>;
    editParameter: Parameter | null;
    productTypes: string[];
};

function DeleteParameterButton({ parameter }: { parameter: Parameter }) {
    return (
        <Dialog>
            <DialogTrigger asChild>
                <Button variant="destructive" size="sm">
                    Delete
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>Delete this parameter?</DialogTitle>
                <DialogDescription>
                    {parameter.product_type} — {parameter.parameter_name} will
                    be removed from the template list. Data lama hasil trial
                    tidak ikut terhapus.
                </DialogDescription>
                <DialogFooter className="gap-2">
                    <DialogClose asChild>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Form
                        {...ParameterController.destroy.form(parameter.id)}
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

export default function AdminParametersIndex({
    parameters,
    editParameter,
    productTypes,
}: PageProps) {
    return (
        <>
            <Head title="Parameters" />

            <div className="space-y-6 p-4">
                <Heading
                    title="Parameter Template"
                    description="Kelola parameter validasi berdasarkan product type."
                />

                <Form
                    {...ParameterController.store.form()}
                    options={{ preserveScroll: true }}
                    resetOnSuccess={[
                        'product_type',
                        'parameter_name',
                        'specification',
                        'sort_order',
                    ]}
                    className="grid gap-4 rounded-lg border p-4 sm:grid-cols-2 lg:grid-cols-4"
                >
                    {({ processing, errors }) => (
                        <>
                            {editParameter && (
                                <input
                                    type="hidden"
                                    name="id"
                                    value={editParameter.id}
                                />
                            )}

                            <div className="grid gap-2">
                                <Label htmlFor="product_type">
                                    Product Type
                                </Label>
                                <Select
                                    name="product_type"
                                    defaultValue={
                                        editParameter?.product_type ??
                                        productTypes[0]
                                    }
                                >
                                    <SelectTrigger id="product_type">
                                        <SelectValue placeholder="Pilih product type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {productTypes.map((type) => (
                                            <SelectItem key={type} value={type}>
                                                {type}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.product_type} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="parameter_name">
                                    Parameter
                                </Label>
                                <Input
                                    id="parameter_name"
                                    name="parameter_name"
                                    required
                                    defaultValue={
                                        editParameter?.parameter_name ?? ''
                                    }
                                />
                                <InputError message={errors.parameter_name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="sort_order">Sort</Label>
                                <Input
                                    id="sort_order"
                                    name="sort_order"
                                    type="number"
                                    defaultValue={
                                        editParameter?.sort_order ?? 0
                                    }
                                />
                                <InputError message={errors.sort_order} />
                            </div>

                            <div className="grid gap-2 sm:col-span-2 lg:col-span-4">
                                <Label htmlFor="specification">
                                    Specification
                                </Label>
                                <Textarea
                                    id="specification"
                                    name="specification"
                                    defaultValue={
                                        editParameter?.specification ?? ''
                                    }
                                />
                                <InputError message={errors.specification} />
                            </div>

                            <div className="flex items-end gap-2">
                                <Button type="submit" disabled={processing}>
                                    {editParameter
                                        ? 'Update Parameter'
                                        : 'Add Parameter'}
                                </Button>
                                {editParameter && (
                                    <Button
                                        type="button"
                                        variant="secondary"
                                        onClick={() =>
                                            router.get(parametersIndex().url)
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
                                    <th className="p-2">Product Type</th>
                                    <th className="p-2">Parameter</th>
                                    <th className="p-2">Specification</th>
                                    <th className="p-2">Sort</th>
                                    <th className="p-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {parameters.data.map((parameter) => (
                                    <tr key={parameter.id} className="border-b">
                                        <td className="p-2">
                                            {parameter.product_type}
                                        </td>
                                        <td className="p-2">
                                            {parameter.parameter_name}
                                        </td>
                                        <td className="p-2 whitespace-pre-line">
                                            {parameter.specification}
                                        </td>
                                        <td className="p-2">
                                            {parameter.sort_order}
                                        </td>
                                        <td className="p-2">
                                            <div className="flex gap-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() =>
                                                        router.get(
                                                            parametersIndex()
                                                                .url,
                                                            {
                                                                edit: parameter.id,
                                                            },
                                                        )
                                                    }
                                                >
                                                    Edit
                                                </Button>
                                                <DeleteParameterButton
                                                    parameter={parameter}
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                                {parameters.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={5}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada parameter aktif.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    <div className="flex items-center justify-between text-sm text-muted-foreground">
                        <span>
                            Page {parameters.current_page} of{' '}
                            {parameters.last_page} ({parameters.total}{' '}
                            parameters)
                        </span>
                        <div className="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={parameters.current_page <= 1}
                                onClick={() =>
                                    router.get(parametersIndex().url, {
                                        page: parameters.current_page - 1,
                                    })
                                }
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={
                                    parameters.current_page >=
                                    parameters.last_page
                                }
                                onClick={() =>
                                    router.get(parametersIndex().url, {
                                        page: parameters.current_page + 1,
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

AdminParametersIndex.layout = {
    breadcrumbs: [{ title: 'Parameters', href: parametersIndex() }],
};
