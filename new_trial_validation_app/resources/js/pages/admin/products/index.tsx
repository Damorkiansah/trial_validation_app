import { Form, Head } from '@inertiajs/react';
import { useState } from 'react';
import ProductController from '@/actions/App/Http/Controllers/Admin/ProductController';
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
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { index as productsIndex } from '@/routes/admin/products';
import type { Paginated } from '@/types';

type Product = {
    id: number;
    product_name: string;
    finish_good_code: string;
};

type PageProps = {
    products: Paginated<Product>;
};

function ProductFormDialog({
    open,
    onOpenChange,
    editingProduct,
}: {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    editingProduct: Product | null;
}) {
    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>
                        {editingProduct ? 'Edit Product' : 'Add Product'}
                    </DialogTitle>
                </DialogHeader>
                <Form
                    {...ProductController.store.form()}
                    options={{ preserveScroll: true }}
                    onSuccess={() => onOpenChange(false)}
                    resetOnSuccess={['product_name', 'finish_good_code']}
                    className="grid gap-4"
                >
                    {({ processing, errors }) => (
                        <>
                            {editingProduct && (
                                <input
                                    type="hidden"
                                    name="id"
                                    value={editingProduct.id}
                                />
                            )}

                            <div className="grid gap-2">
                                <Label htmlFor="product_name">
                                    Product Name
                                </Label>
                                <Input
                                    id="product_name"
                                    name="product_name"
                                    required
                                    defaultValue={
                                        editingProduct?.product_name ?? ''
                                    }
                                />
                                <InputError message={errors.product_name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="finish_good_code">
                                    Finish Good Code
                                </Label>
                                <Input
                                    id="finish_good_code"
                                    name="finish_good_code"
                                    required
                                    defaultValue={
                                        editingProduct?.finish_good_code ?? ''
                                    }
                                />
                                <InputError message={errors.finish_good_code} />
                            </div>

                            <DialogFooter>
                                <Button type="submit" disabled={processing}>
                                    {editingProduct
                                        ? 'Save Changes'
                                        : 'Add Product'}
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}

export default function AdminProductsIndex({ products }: PageProps) {
    const [dialogOpen, setDialogOpen] = useState(false);
    const [editingProduct, setEditingProduct] = useState<Product | null>(null);

    function openCreate() {
        setEditingProduct(null);
        setDialogOpen(true);
    }

    function openEdit(product: Product) {
        setEditingProduct(product);
        setDialogOpen(true);
    }

    return (
        <>
            <Head title="Products" />

            <div className="space-y-6 p-4">
                <Heading
                    title="Products"
                    description="Kelola product dan Finish Good Code untuk input trial."
                />

                <ProductFormDialog
                    open={dialogOpen}
                    onOpenChange={setDialogOpen}
                    editingProduct={editingProduct}
                />

                <Card>
                    <CardHeader className="flex-row items-center justify-end">
                        <Button type="button" onClick={openCreate}>
                            Add Product
                        </Button>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Product</TableHead>
                                    <TableHead>FG Code</TableHead>
                                    <TableHead>Action</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {products.data.map((product) => (
                                    <TableRow key={product.id}>
                                        <TableCell>
                                            {product.product_name}
                                        </TableCell>
                                        <TableCell>
                                            {product.finish_good_code}
                                        </TableCell>
                                        <TableCell>
                                            <div className="flex gap-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    onClick={() =>
                                                        openEdit(product)
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
                                                    title="Delete this product?"
                                                    description={`${product.product_name} (${product.finish_good_code}) will be removed from the template list. Existing trial data is not affected.`}
                                                    confirmLabel="Delete"
                                                    formProps={ProductController.destroy.form(
                                                        product.id,
                                                    )}
                                                />
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {products.data.length === 0 && (
                                    <TableRow>
                                        <TableCell
                                            colSpan={3}
                                            className="p-4 text-center text-muted-foreground"
                                        >
                                            Belum ada product aktif.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        <PaginationFooter
                            url={productsIndex().url}
                            currentPage={products.current_page}
                            lastPage={products.last_page}
                            total={products.total}
                            itemLabel="products"
                        />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

AdminProductsIndex.layout = {
    breadcrumbs: [{ title: 'Products', href: productsIndex() }],
};
