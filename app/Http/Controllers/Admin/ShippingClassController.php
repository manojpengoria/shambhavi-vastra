<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShippingClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $shippingClasses = ShippingClass::query()
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

        return view('admin.shipping-classes.index', compact('shippingClasses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.shipping-classes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        ShippingClass::create($this->validateShippingClass($request));

        return redirect()
            ->route('admin.shipping-classes.index')
            ->with('success', 'Shipping Class created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShippingClass $shippingClass): View
    {
        return view('admin.shipping-classes.edit', compact('shippingClass'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShippingClass $shippingClass): RedirectResponse
    {
        $shippingClass->update($this->validateShippingClass($request, $shippingClass));

        return redirect()
            ->route('admin.shipping-classes.index')
            ->with('success', 'Shipping Class updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingClass $shippingClass): RedirectResponse
    {
        $shippingClass->delete();

        return redirect()
            ->route('admin.shipping-classes.index')
            ->with('success', 'Shipping Class deleted successfully.');
    }

    /**
     * Validate the shipping class request data.
     *
     * @return array{name: string, code: string, extra_charge: string, description: ?string, is_active: bool}
     */
    private function validateShippingClass(Request $request, ?ShippingClass $shippingClass = null): array
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
                Rule::unique('shipping_classes', 'code')->ignore($shippingClass),
            ],
            'extra_charge' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
