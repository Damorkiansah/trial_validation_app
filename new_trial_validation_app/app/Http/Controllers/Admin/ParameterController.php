<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreParameterRequest;
use App\Models\MasterOption;
use App\Models\ValidationParameter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Port of the /admin/parameters (/templates/parameters) block in the legacy
 * app's public/index.php: paginated list with edit-in-place, create-or-
 * update-by-id save, soft delete. Gated by the existing `manage-parameters`
 * Gate (App\Providers\AppServiceProvider), the same check the legacy
 * can_manage_parameters() performs.
 */
class ParameterController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('manage-parameters');

        $parameters = ValidationParameter::query()
            ->where('is_active', 1)
            ->whereNull('deleted_at')
            ->orderBy('product_type')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        $editParameter = null;
        if ($request->filled('edit') && ctype_digit((string) $request->query('edit'))) {
            $editParameter = ValidationParameter::where('id', (int) $request->query('edit'))
                ->where('is_active', 1)
                ->first();
        }

        $productTypes = MasterOption::query()
            ->where('type', 'product_type')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name');

        return Inertia::render('admin/parameters/index', [
            'parameters' => $parameters,
            'editParameter' => $editParameter,
            'productTypes' => $productTypes,
        ]);
    }

    public function store(StoreParameterRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $id = ! empty($data['id']) ? (int) $data['id'] : null;

        $parameter = $id ? ValidationParameter::find($id) : new ValidationParameter;
        $parameter ??= new ValidationParameter;

        $parameter->product_type = $data['product_type'];
        $parameter->parameter_name = $data['parameter_name'];
        $parameter->specification = $data['specification'] ?? null;
        $parameter->sort_order = $data['sort_order'] ?? 0;
        $parameter->is_active = true;
        $parameter->save();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $id ? 'Parameter berhasil diperbarui.' : 'Parameter berhasil ditambahkan.',
        ]);

        return to_route('admin.parameters.index');
    }

    public function destroy(Request $request, ValidationParameter $parameter): RedirectResponse
    {
        Gate::authorize('manage-parameters');

        $parameter->is_active = false;
        $parameter->deleted_at = Carbon::now();
        $parameter->deleted_by = $request->user()->id;
        $parameter->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Parameter berhasil dihapus.']);

        return to_route('admin.parameters.index');
    }
}
