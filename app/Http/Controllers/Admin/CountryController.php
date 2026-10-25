<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $countries = Country::query()
            ->withCount('states')
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where(function ($query) use ($request) {
                    $search = $request->string('search')->trim()->toString();

                    $query->where('country_name', 'like', "%{$search}%")
                        ->orWhere('country_code', 'like', "%{$search}%")
                        ->orWhere('dial_code', 'like', "%{$search}%");
                })
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->integer('status'))
            )
            ->orderBy('country_name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.countries.index', compact('countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.countries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Country::create($this->validateCountry($request));

        return redirect()
            ->route('admin.countries.index')
            ->with('success', 'Country created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $country): View
    {
        return view('admin.countries.edit', compact('country'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Country $country): RedirectResponse
    {
        $country->update($this->validateCountry($request, $country));

        return redirect()
            ->route('admin.countries.index')
            ->with('success', 'Country updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Country $country): RedirectResponse
    {
        if ($country->states()->exists()) {
            return redirect()
                ->route('admin.countries.index')
                ->with('error', 'Country cannot be deleted because it has states.');
        }

        $country->delete();

        return redirect()
            ->route('admin.countries.index')
            ->with('success', 'Country deleted successfully.');
    }

    /**
     * Validate the country request data.
     *
     * @return array{country_name: string, country_code: string, dial_code: string, status: int}
     */
    private function validateCountry(Request $request, ?Country $country = null): array
    {
        $request->merge([
            'country_code' => Str::upper(trim((string) $request->input('country_code'))),
        ]);

        return $request->validate([
            'country_name' => ['required', 'string', 'max:100'],
            'country_code' => [
                'required',
                'alpha',
                'size:2',
                Rule::unique('countries', 'country_code')->ignore($country),
            ],
            'dial_code' => ['required', 'string', 'max:10', 'regex:/^\+?[0-9\-]+$/'],
            'status' => ['required', 'integer', Rule::in([Country::STATUS_ENABLED, Country::STATUS_DISABLED])],
        ]);
    }
}
