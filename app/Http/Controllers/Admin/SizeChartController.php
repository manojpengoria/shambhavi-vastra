<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SizeChart;
use App\Models\VariantValue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator as ValidationValidator;
use Illuminate\View\View;

class SizeChartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $sizeCharts = SizeChart::query()
            ->withCount('products')
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where('name', 'like', '%'.$request->string('search')->trim()->toString().'%')
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->integer('status'))
            )
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.size-charts.index', compact('sizeCharts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Prefill rows with the active values of the "Size" attribute (S, M, L...).
        $defaultSizes = VariantValue::query()
            ->where('status', 707)
            ->whereHas('variantType', fn ($query) => $query->where('slug', 'size'))
            ->orderBy('sort_order')
            ->pluck('variant_value_name')
            ->all();

        return view('admin.size-charts.create', compact('defaultSizes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateSizeChart($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('size-charts', 'public');
        }

        SizeChart::create($validated);

        return redirect()
            ->route('admin.size-charts.index')
            ->with('success', 'Size Chart created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SizeChart $sizeChart): View
    {
        return view('admin.size-charts.edit', compact('sizeChart'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SizeChart $sizeChart): RedirectResponse
    {
        $validated = $this->validateSizeChart($request, $sizeChart);

        if ($request->hasFile('image')) {
            $this->deleteImage($sizeChart->image);
            $validated['image'] = $request->file('image')->store('size-charts', 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($sizeChart->image);
            $validated['image'] = null;
        }

        $sizeChart->update($validated);

        return redirect()
            ->route('admin.size-charts.index')
            ->with('success', 'Size Chart updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SizeChart $sizeChart): RedirectResponse
    {
        if ($sizeChart->products()->exists()) {
            return redirect()
                ->route('admin.size-charts.index')
                ->with('error', 'Size Chart is used by products. Choose another size chart for them first, or set it to Inactive.');
        }

        $this->deleteImage($sizeChart->image);
        $sizeChart->delete();

        return redirect()
            ->route('admin.size-charts.index')
            ->with('success', 'Size Chart deleted successfully.');
    }

    /**
     * Validate the size chart, keeping only filled rows and trimming every cell.
     *
     * @return array{name: string, unit: string, columns: array<int, string>, rows: array<int, array<int, ?string>>, how_to_measure: ?string, status: int, sort_order: int}
     */
    private function validateSizeChart(Request $request, ?SizeChart $sizeChart = null): array
    {
        $columns = array_values(array_map(
            fn ($column) => trim((string) $column),
            (array) $request->input('columns', [])
        ));

        $rows = collect((array) $request->input('rows', []))
            ->map(fn ($row) => array_map(
                fn ($cell) => filled($cell) ? trim((string) $cell) : null,
                array_slice(array_pad(array_values((array) $row), count($columns), null), 0, count($columns))
            ))
            ->reject(fn (array $row) => collect($row)->filter()->isEmpty())
            ->values()
            ->all();

        $request->merge(['columns' => $columns, 'rows' => $rows]);

        $validator = validator($request->all(), [
            'name' => ['required', 'string', 'max:100', Rule::unique('size_charts', 'name')->ignore($sizeChart)],
            'unit' => ['required', Rule::in(array_keys(SizeChart::UNITS))],
            'columns' => ['required', 'array', 'min:2', 'max:'.SizeChart::MAX_COLUMNS],
            'columns.*' => ['required', 'string', 'max:30', 'distinct:ignore_case'],
            'rows' => ['required', 'array', 'min:1', 'max:'.SizeChart::MAX_ROWS],
            'rows.*' => ['array'],
            'rows.*.0' => ['required', 'string', 'max:30', 'distinct:ignore_case'],
            'rows.*.*' => ['nullable', 'string', 'max:30'],
            'how_to_measure' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::in([SizeChart::STATUS_ACTIVE, SizeChart::STATUS_INACTIVE])],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
        ], [
            'columns.min' => 'Add at least two columns, e.g. Size and one measurement.',
            'columns.*.required' => 'Every column needs a heading.',
            'columns.*.distinct' => 'Column headings must be unique.',
            'rows.required' => 'Add at least one size row.',
            'rows.*.0.required' => 'Every row needs a value in the first column (e.g. S, M, L).',
            'rows.*.0.distinct' => 'Each size may only appear once.',
        ]);

        $validator->after(function (ValidationValidator $validator) use ($rows) {
            if ($validator->errors()->isEmpty() && collect($rows)->every(fn (array $row) => collect(array_slice($row, 1))->filter()->isEmpty())) {
                $validator->errors()->add('rows', 'Enter at least one measurement.');
            }
        });

        $validated = $validator->validate();
        unset($validated['image']);

        return $validated;
    }

    /**
     * Delete the stored guide image if present.
     */
    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
