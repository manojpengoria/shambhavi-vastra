<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\PostalCode;
use App\Models\ShippingSetting;
use App\Models\State;
use App\Models\Warehouse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $warehouses = Warehouse::query()
            ->with(['state', 'city', 'postalCode'])
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where(function ($query) use ($request) {
                    $search = $request->string('search')->trim()->toString();

                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                })
            )
            ->when(
                $request->filled('is_active'),
                fn ($query) => $query->where('is_active', $request->boolean('is_active'))
            )
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.warehouses.index', compact('warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.warehouses.create', $this->selectedLocations());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateWarehouse($request);

        DB::transaction(function () use ($validated) {
            $warehouse = Warehouse::create($validated);
            $this->syncDefaultWarehouseSetting($warehouse);
        });

        return redirect()
            ->route('admin.warehouses.index')
            ->with('success', 'Warehouse created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Warehouse $warehouse): View
    {
        return view('admin.warehouses.edit', [
            'warehouse' => $warehouse,
            ...$this->selectedLocations($warehouse),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warehouse $warehouse): RedirectResponse
    {
        $validated = $this->validateWarehouse($request, $warehouse);

        DB::transaction(function () use ($warehouse, $validated) {
            $warehouse->update($validated);
            $this->syncDefaultWarehouseSetting($warehouse);
        });

        return redirect()
            ->route('admin.warehouses.index')
            ->with('success', 'Warehouse updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        if ($warehouse->is_default) {
            return redirect()
                ->route('admin.warehouses.index')
                ->with('error', 'Default warehouse cannot be deleted. Make another warehouse default first.');
        }

        $warehouse->delete();

        return redirect()
            ->route('admin.warehouses.index')
            ->with('success', 'Warehouse deleted successfully.');
    }

    /**
     * Keep the shipping settings default warehouse in sync with the is_default flag.
     */
    private function syncDefaultWarehouseSetting(Warehouse $warehouse): void
    {
        $settings = ShippingSetting::current();

        if ($warehouse->is_default) {
            $settings->update(['default_warehouse_id' => $warehouse->id]);
        } elseif ($settings->default_warehouse_id === $warehouse->id) {
            $settings->update(['default_warehouse_id' => null]);
        }
    }

    /**
     * Load the currently selected location records for the searchable dropdowns.
     *
     * @return array{selectedState: ?State, selectedCity: ?City, selectedPostalCode: ?PostalCode, selectedArea: ?Area}
     */
    private function selectedLocations(?Warehouse $warehouse = null): array
    {
        return [
            'selectedState' => State::with('country')->find(old('state_id', $warehouse?->state_id)),
            'selectedCity' => City::with('state')->find(old('city_id', $warehouse?->city_id)),
            'selectedPostalCode' => PostalCode::find(old('postal_code_id', $warehouse?->postal_code_id)),
            'selectedArea' => Area::find(old('area_id', $warehouse?->area_id)),
        ];
    }

    /**
     * Build an exists rule limited to enabled records, plus the currently assigned one.
     */
    private function enabledOrCurrent(string $table, int $enabledStatus, ?int $currentId): Exists
    {
        return Rule::exists($table, 'id')->where(
            fn ($query) => $query->where('status', $enabledStatus)
                ->when($currentId, fn ($query) => $query->orWhere('id', $currentId))
        );
    }

    /**
     * Validate the warehouse request data.
     *
     * @return array{name: string, code: string, contact_person: string, mobile: string, email: ?string, address_line: string, state_id: int, city_id: int, postal_code_id: int, area_id: ?int, gstin: ?string, pickup_time_slot: ?string, is_default: bool, is_active: bool}
     */
    private function validateWarehouse(Request $request, ?Warehouse $warehouse = null): array
    {
        $request->merge([
            'code' => Str::upper(trim((string) $request->input('code'))),
            'gstin' => $request->filled('gstin') ? Str::upper(trim((string) $request->input('gstin'))) : null,
        ]);

        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\-_]+$/',
                Rule::unique('warehouses', 'code')->ignore($warehouse),
            ],
            'contact_person' => ['required', 'string', 'max:100'],
            'mobile' => ['required', 'string', 'regex:/^\+?[0-9]{10,14}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'address_line' => ['required', 'string', 'max:255'],
            'state_id' => [
                'required',
                'integer',
                $this->enabledOrCurrent('states', State::STATUS_ENABLED, $warehouse?->state_id),
            ],
            'city_id' => [
                'required',
                'integer',
                $this->enabledOrCurrent('cities', City::STATUS_ENABLED, $warehouse?->city_id)
                    ->where('state_id', $request->integer('state_id')),
            ],
            'postal_code_id' => [
                'required',
                'integer',
                $this->enabledOrCurrent('postal_codes', PostalCode::STATUS_ENABLED, $warehouse?->postal_code_id)
                    ->where('city_id', $request->integer('city_id')),
            ],
            'area_id' => [
                'nullable',
                'integer',
                $this->enabledOrCurrent('areas', Area::STATUS_ENABLED, $warehouse?->area_id)
                    ->where('city_id', $request->integer('city_id')),
            ],
            'gstin' => ['nullable', 'regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/'],
            'pickup_time_slot' => ['nullable', 'string', 'max:50'],
            'is_default' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean', 'accepted_if:is_default,1'],
        ], [
            'city_id.exists' => 'The selected city does not belong to the selected state.',
            'postal_code_id.exists' => 'The selected postal code does not belong to the selected city.',
            'area_id.exists' => 'The selected area does not belong to the selected city.',
            'gstin.regex' => 'The GSTIN format is invalid.',
            'mobile.regex' => 'The mobile number must contain 10 to 14 digits.',
            'is_active.accepted_if' => 'The default warehouse must be active.',
        ]);
    }
}
