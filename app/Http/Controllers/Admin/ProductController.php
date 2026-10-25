<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\VariantType;
use App\Models\VariantValue;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Product Listing
     */
    public function index(Request $request): View {}

    /**
     * Show create form.
     */
    public function create(): View
    {
        $categories = Category::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('category_name')
            ->get(['id', 'parent_id', 'category_name']);

        $categoriesByParent = $categories->groupBy(
            fn (Category $category): string => (string) $category->parent_id
        );
        $categoryOptions = collect();

        $appendChildren = function (
            ?int $parentId,
            string $parentPath = ''
        ) use (
            &$appendChildren,
            $categoriesByParent,
            $categoryOptions
        ): void {
            foreach ($categoriesByParent->get((string) $parentId, collect()) as $category) {
                $displayName = $parentPath === ''
                    ? $category->category_name
                    : "{$parentPath} / {$category->category_name}";

                $category->setAttribute('display_name', $displayName);
                $categoryOptions->push($category);
                $appendChildren((int) $category->id, $displayName);
            }
        };

        $appendChildren(null);

        $variantTypes = VariantType::query()
            ->where('status', 707)
            ->with([
                'activeValues' => fn (HasMany $query) => $query->select(
                    'id',
                    'variant_type_id',
                    'variant_value_name'
                ),
            ])
            ->orderBy('sort_order')
            ->orderBy('variant_type_name')
            ->get(['id', 'variant_type_name']);

        $variantTypes->each(function (VariantType $variantType): void {
            $variantType->setAttribute(
                'values_json',
                $variantType->activeValues
                    ->map(fn (VariantValue $value): array => [
                        'id' => (string) $value->id,
                        'text' => $value->variant_value_name,
                    ])
                    ->values()
                    ->toJson()
            );
        });

        return view(
            'admin.products.create',
            compact('categoryOptions', 'variantTypes')
        );
    }
}
