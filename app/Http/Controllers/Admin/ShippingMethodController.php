<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShippingMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $shippingMethods = ShippingMethod::query()
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
            ->orderBy('display_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.shipping-methods.index', compact('shippingMethods'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.shipping-methods.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        ShippingMethod::create($this->validateShippingMethod($request));

        return redirect()
            ->route('admin.shipping-methods.index')
            ->with('success', 'Shipping Method created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShippingMethod $shippingMethod): View
    {
        return view('admin.shipping-methods.edit', compact('shippingMethod'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShippingMethod $shippingMethod): RedirectResponse
    {
        $shippingMethod->update($this->validateShippingMethod($request, $shippingMethod));

        return redirect()
            ->route('admin.shipping-methods.index')
            ->with('success', 'Shipping Method updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingMethod $shippingMethod): RedirectResponse
    {
        $shippingMethod->delete();

        return redirect()
            ->route('admin.shipping-methods.index')
            ->with('success', 'Shipping Method deleted successfully.');
    }

    /**
     * Validate the shipping method request data.
     *
     * @return array{name: string, code: string, customer_text: ?string, display_order: int, is_active: bool}
     */
    private function validateShippingMethod(Request $request, ?ShippingMethod $shippingMethod = null): array
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
                Rule::unique('shipping_methods', 'code')->ignore($shippingMethod),
            ],
            'customer_text' => ['nullable', 'string', 'max:150'],
            'display_order' => ['required', 'integer', 'min:1', 'max:65535'],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
