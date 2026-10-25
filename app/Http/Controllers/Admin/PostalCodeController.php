<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\PostalCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostalCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $postalCodes = PostalCode::query()
            ->with('city.state')
            ->withCount('areas')
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where(function ($query) use ($request) {
                    $search = $request->string('search')->trim()->toString();

                    $query->where('postal_code', 'like', "%{$search}%")
                        ->orWhere('post_office_name', 'like', "%{$search}%");
                })
            )
            ->when(
                $request->filled('city_id'),
                fn ($query) => $query->where('city_id', $request->integer('city_id'))
            )
            ->when(
                $request->filled('delivery_status'),
                fn ($query) => $query->where('delivery_status', $request->integer('delivery_status'))
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->integer('status'))
            )
            ->orderBy('postal_code')
            ->orderBy('post_office_name')
            ->paginate(15)
            ->withQueryString();

        $selectedCity = $request->filled('city_id')
            ? City::with('state')->find($request->integer('city_id'))
            : null;

        return view('admin.postal-codes.index', compact('postalCodes', 'selectedCity'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $selectedCity = City::with('state')->find(old('city_id'));

        return view('admin.postal-codes.create', compact('selectedCity'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        PostalCode::create($this->validatePostalCode($request));

        return redirect()
            ->route('admin.postal-codes.index')
            ->with('success', 'Postal Code created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PostalCode $postalCode): View
    {
        $selectedCity = City::with('state')->find(old('city_id', $postalCode->city_id));

        return view('admin.postal-codes.edit', compact('postalCode', 'selectedCity'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PostalCode $postalCode): RedirectResponse
    {
        $postalCode->update($this->validatePostalCode($request, $postalCode));

        return redirect()
            ->route('admin.postal-codes.index')
            ->with('success', 'Postal Code updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PostalCode $postalCode): RedirectResponse
    {
        $postalCode->delete();

        return redirect()
            ->route('admin.postal-codes.index')
            ->with('success', 'Postal Code deleted successfully.');
    }

    /**
     * Validate the postal code request data.
     *
     * @return array{city_id: int, postal_code: string, post_office_name: ?string, office_type: ?string, delivery_status: int, status: int}
     */
    private function validatePostalCode(Request $request, ?PostalCode $postalCode = null): array
    {
        $request->merge([
            'postal_code' => trim((string) $request->input('postal_code')),
            'post_office_name' => $request->filled('post_office_name')
                ? trim((string) $request->input('post_office_name'))
                : null,
        ]);

        return $request->validate([
            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')->where(
                    fn ($query) => $query->where('status', City::STATUS_ENABLED)
                        ->when($postalCode, fn ($query) => $query->orWhere('id', $postalCode->city_id))
                ),
            ],
            'postal_code' => [
                'required',
                'string',
                'max:10',
                'regex:/^[A-Za-z0-9\- ]+$/',
                Rule::unique('postal_codes', 'postal_code')
                    ->where('city_id', $request->integer('city_id'))
                    ->where('post_office_name', $request->input('post_office_name'))
                    ->ignore($postalCode),
            ],
            'post_office_name' => ['nullable', 'string', 'max:150'],
            'office_type' => ['nullable', Rule::in(array_keys(PostalCode::OFFICE_TYPES))],
            'delivery_status' => [
                'required',
                'integer',
                Rule::in([PostalCode::DELIVERY_AVAILABLE, PostalCode::DELIVERY_UNAVAILABLE]),
            ],
            'status' => [
                'required',
                'integer',
                Rule::in([PostalCode::STATUS_ENABLED, PostalCode::STATUS_DISABLED]),
            ],
        ], [
            'postal_code.unique' => 'This postal code and post office already exist for the selected city.',
        ]);
    }
}
