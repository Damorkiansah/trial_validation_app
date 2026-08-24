<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of the /admin/products (/templates/products) block in the legacy
 * app's public/index.php: paginated list with edit-in-place, create-or-
 * update-by-id-or-name upsert, soft delete. Gated by the existing
 * `manage-templates` Gate (App\Providers\AppServiceProvider), the same
 * check the legacy can_manage_templates() performs.
 */
class ProductController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('manage-templates');

        $products = Product::query()
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->orderBy('product_name')
            ->paginate(20)
            ->withQueryString();

        $editProduct = null;
        if ($request->filled('edit') && ctype_digit((string) $request->query('edit'))) {
            $editProduct = Product::where('id', (int) $request->query('edit'))
                ->where('is_active', 1)
                ->first();
        }

        return Inertia::render('admin/products/index', [
            'products' => $products,
            'editProduct' => $editProduct,
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $id = ! empty($data['id']) ? (int) $data['id'] : null;

        try {
            if ($id) {
                $product = Product::find($id);
                if ($product) {
                    $product->product_name = $data['product_name'];
                    $product->finish_good_code = $data['finish_good_code'];
                    $product->is_active = true;
                    $product->save();
                }
            } else {
                $product = Product::where('product_name', $data['product_name'])->first()
                    ?? new Product(['product_name' => $data['product_name']]);
                $product->finish_good_code = $data['finish_good_code'];
                $product->is_active = true;
                $product->save();
            }
        } catch (QueryException $e) {
            return back()->withErrors(['product_name' => 'Product name sudah digunakan item lain.'])->withInput();
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $id ? 'Product berhasil diperbarui.' : 'Product berhasil ditambahkan.',
        ]);

        return to_route('admin.products.index');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        Gate::authorize('manage-templates');

        $product->is_active = false;
        $product->deleted_at = Carbon::now();
        $product->deleted_by = $request->user()->id;
        $product->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Product berhasil dihapus.']);

        return to_route('admin.products.index');
    }
}
