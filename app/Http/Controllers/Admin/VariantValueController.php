<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VariantType;
use App\Models\VariantValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VariantValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $variantValues = VariantValue::with('variantType')
            ->when(
                $request->filled('variant_type_id'),
                fn ($query) => $query->where(
                    'variant_type_id',
                    $request->integer('variant_type_id')
                )
            )
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $variantTypes = VariantType::orderBy('variant_type_name')->get();

        return view(
            'admin.variant-values.index',
            compact('variantValues', 'variantTypes')
        );

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $variantTypes = VariantType::where('status', 707)
            ->orderBy('variant_type_name')
            ->get();

        return view(
            'admin.variant-values.create',
            compact('variantTypes')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'variant_type_id' => [
                'required',
                'integer',
                Rule::exists('variant_types', 'id')
                    ->where('status', 707),
            ],
            'variant_value_name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120'],
            'color_code' => [
                'nullable',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'status' => ['required', Rule::in([707, 505])],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $validated['slug'] = $this->generateUniqueSlug(
            $validated['variant_value_name'],
            (int) $validated['variant_type_id']
        );

        VariantValue::create($validated);
        return redirect()
            ->route('admin.variant-values.index')
            ->with('success', 'Variant Value created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VariantValue $variantValue): View
    {
        $variantTypes = VariantType::orderBy('variant_type_name')->get();

        return view(
            'admin.variant-values.edit',
            compact('variantValue', 'variantTypes')
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        VariantValue $variantValue
    ): RedirectResponse {
        $validated = $request->validate([
            'variant_type_id' => [
                'required',
                'integer',
                'exists:variant_types,id',
            ],
            'variant_value_name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120'],
            'color_code' => [
                'nullable',
                'regex:/^#[0-9A-Fa-f]{6}$/',
            ],
            'status' => ['required', Rule::in([707, 505])],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $validated['slug'] = $this->generateUniqueSlug(
            $validated['variant_value_name'],
            (int) $validated['variant_type_id'],
            $variantValue->id
        );

        $variantValue->update($validated);
        return redirect()
            ->route('admin.variant-values.index')
            ->with('success', 'Variant Value updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VariantValue $variantValue): RedirectResponse
    {
        $variantValue->delete();

        return redirect()
            ->route('admin.variant-values.index')
            ->with('success', 'Variant Value deleted successfully.');
    }

    /**
     *  Generated slug for unique.
     */
     
    private function generateUniqueSlug(string $value, int $variantTypeId, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);

        if ($baseSlug === '') {
            $baseSlug = 'variant';
        }

        $slug = $baseSlug;
        $counter = 2;

        while (
            VariantValue::query()
                ->where('variant_type_id', $variantTypeId)
                ->where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) => $query->where('id', '!=', $ignoreId)
                )
                ->exists()
        ) {
            $suffix = '-'.$counter;

            $slug = Str::limit(
                $baseSlug,
                120 - strlen($suffix),
                ''
            ).$suffix;

            $counter++;
        }

        return $slug;
    }
}
