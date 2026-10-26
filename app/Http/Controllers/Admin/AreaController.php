<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\PostalCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $areas = Area::query()
            ->with(['city.state', 'postalCode'])
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where(function ($query) use ($request) {
                    $search = $request->string('search')->trim()->toString();

                    $query->where('area_name', 'like', "%{$search}%")
                        ->orWhere('area_code', 'like', "%{$search}%");
                })
            )
            ->when(
                $request->filled('city_id'),
                fn ($query) => $query->where('city_id', $request->integer('city_id'))
            )
            ->when(
                $request->filled('area_type'),
                fn ($query) => $query->where('area_type', $request->string('area_type')->toString())
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->integer('status'))
            )
            ->orderBy('sort_order')
            ->orderBy('area_name')
            ->paginate(15)
            ->withQueryString();

        $selectedCity = $request->filled('city_id')
            ? City::with('state')->find($request->integer('city_id'))
            : null;

        return view('admin.areas.index', compact('areas', 'selectedCity'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $selectedCity = City::with('state')->find(old('city_id'));
        $selectedPostalCode = PostalCode::find(old('postal_code_id'));

        return view('admin.areas.create', compact('selectedCity', 'selectedPostalCode'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Area::create($this->validateArea($request));

        return redirect()
            ->route('admin.areas.index')
            ->with('success', 'Area created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Area $area): View
    {
        $selectedCity = City::with('state')->find(old('city_id', $area->city_id));
        $selectedPostalCode = PostalCode::find(old('postal_code_id', $area->postal_code_id));

        return view('admin.areas.edit', compact('area', 'selectedCity', 'selectedPostalCode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Area $area): RedirectResponse
    {
        $area->update($this->validateArea($request, $area));

        return redirect()
            ->route('admin.areas.index')
            ->with('success', 'Area updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area): RedirectResponse
    {
        $area->delete();

        return redirect()
            ->route('admin.areas.index')
            ->with('success', 'Area deleted successfully.');
    }

    /**
     * Validate the area request data.
     *
     * @return array{city_id: int, postal_code_id: ?int, area_name: string, area_code: string, area_type: string, latitude: ?string, longitude: ?string, delivery_available: int, status: int, sort_order: int}
     */
    private function validateArea(Request $request, ?Area $area = null): array
    {
        $request->merge([
            'area_code' => Str::upper(trim((string) $request->input('area_code'))),
        ]);

        return $request->validate([
            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')->where(
                    fn ($query) => $query->where('status', City::STATUS_ENABLED)
                        ->when($area, fn ($query) => $query->orWhere('id', $area->city_id))
                ),
            ],
            'postal_code_id' => [
                'nullable',
                'integer',
                Rule::exists('postal_codes', 'id')
                    ->where('city_id', $request->integer('city_id'))
                    ->where(
                        fn ($query) => $query->where('status', PostalCode::STATUS_ENABLED)
                            ->when($area?->postal_code_id, fn ($query) => $query->orWhere('id', $area->postal_code_id))
                    ),
            ],
            'area_name' => ['required', 'string', 'max:150'],
            'area_code' => [
                'required',
                'string',
                'max:30',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('areas', 'area_code')->ignore($area),
            ],
            'area_type' => [
                'required',
                Rule::in([Area::TYPE_LOCALITY, Area::TYPE_COLONY, Area::TYPE_SECTOR, Area::TYPE_VILLAGE]),
            ],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'delivery_available' => [
                'required',
                'integer',
                Rule::in([Area::DELIVERY_AVAILABLE, Area::DELIVERY_UNAVAILABLE]),
            ],
            'status' => ['required', 'integer', Rule::in([Area::STATUS_ENABLED, Area::STATUS_DISABLED])],
            'sort_order' => ['required', 'integer', 'min:0'],
        ], [
            'postal_code_id.exists' => 'The selected postal code does not belong to the selected city.',
        ]);
    }
}
