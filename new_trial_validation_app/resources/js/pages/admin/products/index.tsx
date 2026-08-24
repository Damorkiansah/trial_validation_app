import { Form, Head, router } from '@inertiajs/react';
import ProductController from '@/actions/App/Http/Controllers/Admin/ProductController';
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
import { index as productsIndex } from '@/routes/admin/products';

type Product = {
    id: number;
    product_name: string;
    finish_good_code: string;
};

type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
};

type PageProps = {
    products: Paginated<Product>;
    editProduct: Product | null;
};

function DeleteProductButton({ product }: { product: Product }) {
    return (
        <Dialog>
            <DialogTrigger asChild>
                <Button variant="destructive" size="sm">
                    Delete
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>Delete this product?</DialogTitle>
                <DialogDescription>
                    {product.product_name} ({product.finish_good_code}) will be
                    removed from the template list. Existing trial data is not
                    affected.
                </DialogDescription>
                <DialogFooter className="gap-2">
                    <DialogClose asChild>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                    <Form
                        {...ProductController.destroy.form(product.id)}
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

export default function AdminProductsIndex({
    products,
    editProduct,
}: PageProps) {
    return (
        <>
            <Head title="Products" />

            <div className="space-y-6 p-4">
                <Heading
                    title="Products"
                    description="Kelola product dan Finish Good Code untuk input trial."
                />

                <Form
                    {...ProductController.store.form()}
                    options={{ preserveScroll: true }}
                    resetOnSuccess={['product_name', 'finish_good_code']}
                    className="grid gap-4 rounded-lg border p-4 sm:grid-cols-2 lg:grid-cols-5"
                >
                    {({ processing, errors }) => (
                        <>
                            {editProduct && (
                                <input
                                    type="hidden"
                                    name="id"
                                    value={editProduct.id}
                                />
                            )}

                            <div className="grid gap-2 lg:col-span-2">
                                <Label htmlFor="product_name">
                                    Product Name
                                </Label>
                                <Input
                                    id="product_name"
                                    name="product_name"
                                    required
                                    defaultValue={
                                        editProduct?.product_name ?? ''
                                    }
                                />
                                <InputError message={errors.product_name} />
                            </div>

                            <div className="grid gap-2 lg:col-span-2">
                                <Label htmlFor="finish_good_code">
                                    Finish Good Code
                                </Label>
                                <Input
                                    id="finish_good_code"
                                    name="finish_good_code"
                                    required
                                    defaultValue={
                                        editProduct?.finish_good_code ?? ''
                                    }
                                />
                                <InputError message={errors.finish_good_code} />
                            </div>

                            <div className="flex items-end gap-2">
                                <Button type="submit" disabled={processing}>
                                    {editProduct
                                        ? 'Update Product'
                                        : 'Add Product'}
                                </Button>
                                {editProduct && (
                                    <Button
                                        type="button"
                                        variant="secondary"
                                        onClick={() =>
                                            router.get(productsIndex().url)
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
                                    <th className="p-2">Product</th>
                                    <th className="p-2">FG Code</th>
                                    <th className="p-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {products.data.map((product) => (
                                    <tr key={product.id} className="border-b">
                                        <td className="p-2">
                                            {product.product_name}
                                        </td>
                                        <td className="p-2">
                                            {product.finish_good_code}
                                        </td>
                                        <td className="p-2">
                                            <div className="flex gap-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() =>
                                                        router.get(
                                                            productsIndex().url,
                                                            {
                                                                edit: product.id,
                                                            },
                                                        )
                                                    }
                                                >
                                                    Edit
                                                </Button>
                                                <DeleteProductButton
                                                    product={product}
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                                {products.data.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={3}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada product aktif.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    <div className="flex items-center justify-between text-sm text-muted-foreground">
                        <span>
                            Page {products.current_page} of {products.last_page}{' '}
                            ({products.total} products)
                        </span>
                        <div className="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={products.current_page <= 1}
                                onClick={() =>
                                    router.get(productsIndex().url, {
                                        page: products.current_page - 1,
                                    })
                                }
                            >
                                Previous
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={
                                    products.current_page >= products.last_page
                                }
                                onClick={() =>
                                    router.get(productsIndex().url, {
                                        page: products.current_page + 1,
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

AdminProductsIndex.layout = {
    breadcrumbs: [{ title: 'Products', href: productsIndex() }],
};
