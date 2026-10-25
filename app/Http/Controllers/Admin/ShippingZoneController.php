<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\ShippingZone;
use App\Models\State;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShippingZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $shippingZones = ShippingZone::query()
            ->withCount(['states', 'cities'])
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where(function ($query) use ($request) {
                    $search = $request->string('search')->trim()->toString();

                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })
            )
            ->when(
                $request->filled('coverage_type'),
                fn ($query) => $query->where('coverage_type', $request->string('coverage_type')->toString())
            )
            ->when(
                $request->filled('is_active'),
                fn ($query) => $query->where('is_active', $request->boolean('is_active'))
            )
            ->orderBy('priority')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.shipping-zones.index', compact('shippingZones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.shipping-zones.create', $this->selectedCoverage());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateShippingZone($request);

        DB::transaction(function () use ($validated) {
            $shippingZone = ShippingZone::create(Arr::except($validated, ['state_ids', 'city_ids']));
            $this->syncCoverage($shippingZone, $validated);
        });

        return redirect()
            ->route('admin.shipping-zones.index')
            ->with('success', 'Shipping Zone created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShippingZone $shippingZone): View
    {
        return view('admin.shipping-zones.edit', [
            'shippingZone' => $shippingZone,
            ...$this->selectedCoverage($shippingZone),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShippingZone $shippingZone): RedirectResponse
    {
        $validated = $this->validateShippingZone($request, $shippingZone);

        DB::transaction(function () use ($shippingZone, $validated) {
            $shippingZone->update(Arr::except($validated, ['state_ids', 'city_ids']));
            $this->syncCoverage($shippingZone, $validated);
        });

        return redirect()
            ->route('admin.shipping-zones.index')
            ->with('success', 'Shipping Zone updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingZone $shippingZone): RedirectResponse
    {
        $shippingZone->delete();

        return redirect()
            ->route('admin.shipping-zones.index')
            ->with('success', 'Shipping Zone deleted successfully.');
    }

    /**
     * Sync covered states and cities according to the coverage type.
     *
     * @param  array{coverage_type: string, state_ids?: array<int, int>, city_ids?: array<int, int>}  $validated
     */
    private function syncCoverage(ShippingZone $shippingZone, array $validated): void
    {
        $coverageType = $validated['coverage_type'];

        $shippingZone->states()->sync(
            $coverageType === ShippingZone::COVERAGE_STATE ? $validated['state_ids'] : []
        );
        $shippingZone->cities()->sync(
            $coverageType === ShippingZone::COVERAGE_CITY ? $validated['city_ids'] : []
        );
    }

    /**
     * Load the selected states and cities for the searchable multi-selects.
     *
     * @return array{selectedStates: Collection<int, State>, selectedCities: Collection<int, City>}
     */
    private function selectedCoverage(?ShippingZone $shippingZone = null): array
    {
        $stateIds = old('state_ids', $shippingZone?->states()->pluck('states.id')->all() ?? []);
        $cityIds = old('city_ids', $shippingZone?->cities()->pluck('cities.id')->all() ?? []);

        return [
            'selectedStates' => State::with('country')->whereIn('id', (array) $stateIds)->orderBy('state_name')->get(),
            'selectedCities' => City::with('state')->whereIn('id', (array) $cityIds)->orderBy('city_name')->get(),
        ];
    }

    /**
     * Validate the shipping zone request data.
     *
     * @return array{name: string, code: string, coverage_type: string, priority: int, description: ?string, is_active: bool, state_ids?: array<int, int>, city_ids?: array<int, int>}
     */
    private function validateShippingZone(Request $request, ?ShippingZone $shippingZone = null): array
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
                Rule::unique('shipping_zones', 'code')->ignore($shippingZone),
            ],
            'coverage_type' => ['required', Rule::in(array_keys(ShippingZone::COVERAGE_TYPES))],
            'state_ids' => ['exclude_unless:coverage_type,'.ShippingZone::COVERAGE_STATE, 'required', 'array'],
            'state_ids.*' => ['exclude_unless:coverage_type,'.ShippingZone::COVERAGE_STATE, 'integer', 'distinct', Rule::exists('states', 'id')],
            'city_ids' => ['exclude_unless:coverage_type,'.ShippingZone::COVERAGE_CITY, 'required', 'array'],
            'city_ids.*' => ['exclude_unless:coverage_type,'.ShippingZone::COVERAGE_CITY, 'integer', 'distinct', Rule::exists('cities', 'id')],
            'priority' => ['required', 'integer', 'min:1', 'max:65535'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ], [
            'state_ids.required' => 'Select at least one state for a state coverage zone.',
            'city_ids.required' => 'Select at least one city for a city coverage zone.',
        ]);
    }
}
