<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $cities = City::query()
            ->with('state.country')
            ->withCount(['postalCodes', 'areas'])
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where(function ($query) use ($request) {
                    $search = $request->string('search')->trim()->toString();

                    $query->where('city_name', 'like', "%{$search}%")
                        ->orWhere('city_code', 'like', "%{$search}%");
                })
            )
            ->when(
                $request->filled('state_id'),
                fn ($query) => $query->where('state_id', $request->integer('state_id'))
            )
            ->when(
                $request->filled('city_type'),
                fn ($query) => $query->where('city_type', $request->string('city_type')->toString())
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->integer('status'))
            )
            ->orderBy('sort_order')
            ->orderBy('city_name')
            ->paginate(15)
            ->withQueryString();

        $selectedState = $request->filled('state_id')
            ? State::with('country')->find($request->integer('state_id'))
            : null;

        return view('admin.cities.index', compact('cities', 'selectedState'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $selectedState = State::with('country')->find(old('state_id'));

        return view('admin.cities.create', compact('selectedState'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        City::create($this->validateCity($request));

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'City created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(City $city): View
    {
        $selectedState = State::with('country')->find(old('state_id', $city->state_id));

        return view('admin.cities.edit', compact('city', 'selectedState'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city): RedirectResponse
    {
        $city->update($this->validateCity($request, $city));

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'City updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city): RedirectResponse
    {
        if ($city->postalCodes()->exists() || $city->areas()->exists()) {
            return redirect()
                ->route('admin.cities.index')
                ->with('error', 'City cannot be deleted because it has postal codes or areas.');
        }

        $city->delete();

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'City deleted successfully.');
    }

    /**
     * Validate the city request data.
     *
     * @return array{state_id: int, city_name: string, city_code: string, city_type: string, latitude: ?string, longitude: ?string, status: int, sort_order: int}
     */
    private function validateCity(Request $request, ?City $city = null): array
    {
        $request->merge([
            'city_code' => Str::upper(trim((string) $request->input('city_code'))),
        ]);

        return $request->validate([
            'state_id' => [
                'required',
                'integer',
                Rule::exists('states', 'id')->where(
                    fn ($query) => $query->where('status', State::STATUS_ENABLED)
                        ->when($city, fn ($query) => $query->orWhere('id', $city->state_id))
                ),
            ],
            'city_name' => ['required', 'string', 'max:150'],
            'city_code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('cities', 'city_code')->ignore($city),
            ],
            'city_type' => ['required', Rule::in([City::TYPE_CITY, City::TYPE_TOWN, City::TYPE_VILLAGE])],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'status' => ['required', 'integer', Rule::in([City::STATUS_ENABLED, City::STATUS_DISABLED])],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);
    }
}
