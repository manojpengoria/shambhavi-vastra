<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VariantType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VariantTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $variantTypes = VariantType::withCount('values')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15);
        return view('admin.variant-types.index', compact('variantTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.variant-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'variant_type_name' => ['required', 'string', 'max:100', 'unique:variant_types,variant_type_name'],
            'slug' => ['nullable', 'string', 'max:120', 'unique:variant_types,slug'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in([707, 505])],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $validated['slug'] = $this->generateUniqueSlug(
            $validated['slug'] ?: $validated['variant_type_name']
        );

        VariantType::create($validated);

        return redirect()
            ->route('admin.variant-type.index')
            ->with('success', 'Variant Type created successfully.');
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
    public function edit(VariantType $variantType): View
    {
        return view('admin.variant-types.edit', compact('variantType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, VariantType $variantType): RedirectResponse
    {
        $validated = $request->validate([
            'variant_type_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('variant_types', 'variant_type_name')->ignore($variantType->id),
            ],

            'slug' => [
                'nullable',
                'string',
                'max:120',
                Rule::unique('variant_types', 'slug')->ignore($variantType->id),
            ],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in([707, 505])],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $validated['slug'] = $this->generateUniqueSlug(
            $validated['variant_type_name'],
            $variantType->id
        );

        $variantType->update($validated);

        return redirect()
            ->route('admin.variant-type.index')
            ->with('success', 'Variant Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VariantType $variantType): RedirectResponse
    {
        // Do not delete a type that contains values.
        if ($variantType->values()->exists()) {
            return back()->with(
                'error',
                'Delete its variant values first.'
            );
        }

        $variantType->delete();
        return redirect()
            ->route('admin.variant-type.index')
            ->with('success', 'Variant Type deleted successfully.');
    }

    /**
     *  Generated slug for unique.
     */
     
    private function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);

        if ($baseSlug === '') {
            $baseSlug = 'variant';
        }

        $slug = $baseSlug;
        $counter = 2;

        while (
            VariantType::query()
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
