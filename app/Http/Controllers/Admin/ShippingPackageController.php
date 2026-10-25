<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingPackage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShippingPackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $shippingPackages = ShippingPackage::query()
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where(function ($query) use ($request) {
                    $search = $request->string('search')->trim()->toString();

                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })
            )
            ->when(
                $request->filled('is_active'),
                fn ($query) => $query->where('is_active', $request->boolean('is_active'))
            )
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.shipping-packages.index', compact('shippingPackages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.shipping-packages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        ShippingPackage::create($this->validateShippingPackage($request));

        return redirect()
            ->route('admin.shipping-packages.index')
            ->with('success', 'Package created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShippingPackage $shippingPackage): View
    {
        return view('admin.shipping-packages.edit', compact('shippingPackage'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShippingPackage $shippingPackage): RedirectResponse
    {
        $shippingPackage->update($this->validateShippingPackage($request, $shippingPackage));

        return redirect()
            ->route('admin.shipping-packages.index')
            ->with('success', 'Package updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingPackage $shippingPackage): RedirectResponse
    {
        $shippingPackage->delete();

        return redirect()
            ->route('admin.shipping-packages.index')
            ->with('success', 'Package deleted successfully.');
    }

    /**
     * Validate the shipping package request data.
     *
     * @return array{name: string, code: string, length: string, breadth: string, height: string, box_weight: string, max_weight: ?string, cost: ?string, is_active: bool}
     */
    private function validateShippingPackage(Request $request, ?ShippingPackage $shippingPackage = null): array
    {
        $request->merge([
            'code' => Str::upper(trim((string) $request->input('code'))),
        ]);

        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\-_]+$/',
                Rule::unique('shipping_packages', 'code')->ignore($shippingPackage),
            ],
            'length' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'breadth' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'height' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'box_weight' => ['required', 'numeric', 'min:0', 'max:99999.999'],
            'max_weight' => ['nullable', 'numeric', 'gt:box_weight', 'max:99999.999'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'is_active' => ['required', 'boolean'],
        ], [
            'max_weight.gt' => 'The max weight must be greater than the box weight.',
        ]);
    }
}
