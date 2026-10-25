<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourierPartner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourierPartnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $courierPartners = CourierPartner::query()
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where(function ($query) use ($request) {
                    $search = $request->string('search')->trim()->toString();

                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })
            )
            ->when(
                $request->filled('integration_type'),
                fn ($query) => $query->where('integration_type', $request->string('integration_type')->toString())
            )
            ->when(
                $request->filled('is_active'),
                fn ($query) => $query->where('is_active', $request->boolean('is_active'))
            )
            ->orderBy('priority')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.courier-partners.index', compact('courierPartners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.courier-partners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCourierPartner($request);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('courier-partners', 'public');
        }

        CourierPartner::create($validated);

        return redirect()
            ->route('admin.courier-partners.index')
            ->with('success', 'Courier Partner created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CourierPartner $courierPartner): View
    {
        return view('admin.courier-partners.edit', compact('courierPartner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourierPartner $courierPartner): RedirectResponse
    {
        $validated = $this->validateCourierPartner($request, $courierPartner);

        // Blank credential fields keep the stored (encrypted) values.
        foreach (['api_key', 'api_secret'] as $credential) {
            if (blank($validated[$credential] ?? null)) {
                unset($validated[$credential]);
            }
        }

        if ($request->hasFile('logo')) {
            $this->deleteLogo($courierPartner->logo);
            $validated['logo'] = $request->file('logo')->store('courier-partners', 'public');
        } elseif ($request->boolean('remove_logo')) {
            $this->deleteLogo($courierPartner->logo);
            $validated['logo'] = null;
        }

        $courierPartner->update($validated);

        return redirect()
            ->route('admin.courier-partners.index')
            ->with('success', 'Courier Partner updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CourierPartner $courierPartner): RedirectResponse
    {
        $this->deleteLogo($courierPartner->logo);
        $courierPartner->delete();

        return redirect()
            ->route('admin.courier-partners.index')
            ->with('success', 'Courier Partner deleted successfully.');
    }

    /**
     * Validate the courier partner request data.
     *
     * @return array{name: string, code: string, integration_type: string, api_key?: ?string, api_secret?: ?string, tracking_url: ?string, priority: int, max_weight: ?string, max_cod_amount: ?string, supports_cod: bool, supports_reverse_pickup: bool, supports_express: bool, is_active: bool}
     */
    private function validateCourierPartner(Request $request, ?CourierPartner $courierPartner = null): array
    {
        $request->merge([
            'code' => Str::upper(trim((string) $request->input('code'))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\-_]+$/',
                Rule::unique('courier_partners', 'code')->ignore($courierPartner),
            ],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'integration_type' => ['required', Rule::in(array_keys(CourierPartner::INTEGRATION_TYPES))],
            'api_key' => [
                Rule::requiredIf(
                    fn () => $request->input('integration_type') === CourierPartner::TYPE_API
                        && blank($courierPartner?->api_key)
                ),
                'nullable',
                'string',
                'max:500',
            ],
            'api_secret' => ['nullable', 'string', 'max:500'],
            'tracking_url' => ['nullable', 'string', 'max:255', 'starts_with:http://,https://', 'regex:/\{AWB\}/'],
            'priority' => ['required', 'integer', 'min:1', 'max:65535'],
            'max_weight' => ['nullable', 'numeric', 'min:0', 'max:99999.999'],
            'max_cod_amount' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'supports_cod' => ['required', 'boolean'],
            'supports_reverse_pickup' => ['required', 'boolean'],
            'supports_express' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ], [
            'tracking_url.regex' => 'The tracking URL must contain the {AWB} placeholder.',
        ]);

        unset($validated['logo']);

        return $validated;
    }

    /**
     * Delete the stored logo if present.
     */
    private function deleteLogo(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
