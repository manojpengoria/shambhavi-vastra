<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $states = State::query()
            ->with('country')
            ->withCount('cities')
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where(function ($query) use ($request) {
                    $search = $request->string('search')->trim()->toString();

                    $query->where('state_name', 'like', "%{$search}%")
                        ->orWhere('state_code', 'like', "%{$search}%")
                        ->orWhere('capital', 'like', "%{$search}%");
                })
            )
            ->when(
                $request->filled('country_id'),
                fn ($query) => $query->where('country_id', $request->integer('country_id'))
            )
            ->when(
                $request->filled('state_type'),
                fn ($query) => $query->where('state_type', $request->string('state_type')->toString())
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where('status', $request->integer('status'))
            )
            ->orderBy('sort_order')
            ->orderBy('state_name')
            ->paginate(15)
            ->withQueryString();

        $countries = Country::orderBy('country_name')->get();

        return view('admin.states.index', compact('states', 'countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $countries = Country::enabled()->orderBy('country_name')->get();

        return view('admin.states.create', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        State::create($this->validateState($request));

        return redirect()
            ->route('admin.states.index')
            ->with('success', 'State created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(State $state): View
    {
        $countries = Country::query()
            ->where(fn ($query) => $query->enabled()->orWhere('id', $state->country_id))
            ->orderBy('country_name')
            ->get();

        return view('admin.states.edit', compact('state', 'countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, State $state): RedirectResponse
    {
        $state->update($this->validateState($request, $state));

        return redirect()
            ->route('admin.states.index')
            ->with('success', 'State updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(State $state): RedirectResponse
    {
        if ($state->cities()->exists()) {
            return redirect()
                ->route('admin.states.index')
                ->with('error', 'State cannot be deleted because it has cities.');
        }

        $state->delete();

        return redirect()
            ->route('admin.states.index')
            ->with('success', 'State deleted successfully.');
    }

    /**
     * Validate the state request data.
     *
     * @return array{country_id: int, state_name: string, state_code: string, gst_state_code: ?string, state_type: string, capital: ?string, status: int, sort_order: int}
     */
    private function validateState(Request $request, ?State $state = null): array
    {
        $request->merge([
            'state_code' => Str::upper(trim((string) $request->input('state_code'))),
        ]);

        return $request->validate([
            'country_id' => [
                'required',
                'integer',
                Rule::exists('countries', 'id')->where(
                    fn ($query) => $query->where('status', Country::STATUS_ENABLED)
                        ->when($state, fn ($query) => $query->orWhere('id', $state->country_id))
                ),
            ],
            'state_name' => ['required', 'string', 'max:100'],
            'state_code' => [
                'required',
                'string',
                'max:10',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('states', 'state_code')->ignore($state),
            ],
            'gst_state_code' => [
                'nullable',
                'digits:2',
                Rule::unique('states', 'gst_state_code')->ignore($state),
            ],
            'state_type' => ['required', Rule::in([State::TYPE_STATE, State::TYPE_UT])],
            'capital' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'integer', Rule::in([State::STATUS_ENABLED, State::STATUS_DISABLED])],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);
    }
}
