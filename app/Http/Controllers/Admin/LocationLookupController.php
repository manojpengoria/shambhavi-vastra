<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use App\Models\PostalCode;
use App\Models\State;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationLookupController extends Controller
{
    /**
     * Number of results returned per lookup page.
     */
    private const PER_PAGE = 20;

    /**
     * Search states for select2 dropdowns.
     */
    public function states(Request $request): JsonResponse
    {
        $states = State::query()
            ->with('country')
            ->unless($request->boolean('include_disabled'), fn ($query) => $query->enabled())
            ->when(
                $this->searchTerm($request),
                fn ($query, string $search) => $query->where(
                    fn ($query) => $query->where('state_name', 'like', "%{$search}%")
                        ->orWhere('state_code', 'like', "%{$search}%")
                )
            )
            ->orderBy('state_name')
            ->simplePaginate(self::PER_PAGE);

        return $this->select2Response($states);
    }

    /**
     * Search cities for select2 dropdowns.
     */
    public function cities(Request $request): JsonResponse
    {
        $cities = City::query()
            ->with('state')
            ->when(
                $request->filled('state_id'),
                fn ($query) => $query->where('state_id', $request->integer('state_id'))
            )
            ->unless($request->boolean('include_disabled'), fn ($query) => $query->enabled())
            ->when(
                $this->searchTerm($request),
                fn ($query, string $search) => $query->where(
                    fn ($query) => $query->where('city_name', 'like', "%{$search}%")
                        ->orWhere('city_code', 'like', "%{$search}%")
                )
            )
            ->orderBy('city_name')
            ->simplePaginate(self::PER_PAGE);

        return $this->select2Response($cities);
    }

    /**
     * Search postal codes of a city for select2 dropdowns.
     */
    public function postalCodes(Request $request): JsonResponse
    {
        $request->validate([
            'city_id' => ['required', 'integer'],
        ]);

        $postalCodes = PostalCode::query()
            ->where('city_id', $request->integer('city_id'))
            ->unless($request->boolean('include_disabled'), fn ($query) => $query->enabled())
            ->when(
                $this->searchTerm($request),
                fn ($query, string $search) => $query->where(
                    fn ($query) => $query->where('postal_code', 'like', "%{$search}%")
                        ->orWhere('post_office_name', 'like', "%{$search}%")
                )
            )
            ->orderBy('postal_code')
            ->orderBy('post_office_name')
            ->simplePaginate(self::PER_PAGE);

        return $this->select2Response($postalCodes);
    }

    /**
     * Search areas of a city for select2 dropdowns.
     */
    public function areas(Request $request): JsonResponse
    {
        $request->validate([
            'city_id' => ['required', 'integer'],
        ]);

        $areas = Area::query()
            ->where('city_id', $request->integer('city_id'))
            ->unless($request->boolean('include_disabled'), fn ($query) => $query->enabled())
            ->when(
                $this->searchTerm($request),
                fn ($query, string $search) => $query->where(
                    fn ($query) => $query->where('area_name', 'like', "%{$search}%")
                        ->orWhere('area_code', 'like', "%{$search}%")
                )
            )
            ->orderBy('area_name')
            ->simplePaginate(self::PER_PAGE);

        return $this->select2Response($areas);
    }

    /**
     * Get the trimmed search term, or null when empty.
     */
    private function searchTerm(Request $request): ?string
    {
        $search = $request->string('q')->trim()->toString();

        return $search === '' ? null : $search;
    }

    /**
     * Format paginated models in the select2 results structure.
     *
     * @param  Paginator<int, State|City|PostalCode|Area>  $paginator
     */
    private function select2Response(Paginator $paginator): JsonResponse
    {
        return response()->json([
            'results' => collect($paginator->items())
                ->map(fn (Model $model) => [
                    'id' => $model->getKey(),
                    'text' => $model->selectLabel(),
                ])
                ->values(),
            'pagination' => [
                'more' => $paginator->hasMorePages(),
            ],
        ]);
    }
}
