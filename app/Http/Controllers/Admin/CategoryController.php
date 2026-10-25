<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Category listing.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $categories = Category::query()
            ->with('parent')
            ->withCount('children')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('category_name', 'like', "%{$search}%")
                        ->orWhere('seo_url', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('category_name')
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.categories.index',
            compact('categories', 'search')
        );
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        $categories = Category::query()
            ->orderBy('sort_order')
            ->orderBy('category_name')
            ->get(['id', 'parent_id', 'category_name']);

        return view(
            'admin.categories.create',
            ['categoryOptions' => $this->buildCategoryOptions($categories)]
        );
    }

    /**
     * Store category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCategory($request);
        $validated['seo_url'] = $this->generateUniqueSeoUrl(
            $validated['seo_url'] ?: $validated['category_name']
        );
        $validated['thumb_image'] = $request->file('thumb_image')
            ? $request->file('thumb_image')->store(
                'categories/thumbnails',
                'public'
            )
            : null;
        $validated['large_image'] = $request->file('large_image')
            ? $request->file('large_image')->store(
                'categories/large',
                'public'
            )
            : null;

        Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show category details.
     */
    public function show(Category $category): View
    {
        $category->load(['parent', 'children']);

        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show edit form.
     */
    public function edit(Category $category): View
    {
        $excludedIds = $this->getDescendantIds($category);
        // A category cannot be its own parent or its descendant's parent.
        $excludedIds[] = $category->id;
        $categories = Category::query()
            ->whereNotIn('id', $excludedIds)
            ->orderBy('sort_order')
            ->orderBy('category_name')
            ->get(['id', 'parent_id', 'category_name']);
        $categoryOptions = $this->buildCategoryOptions($categories);

        return view(
            'admin.categories.edit',
            compact('category', 'categoryOptions')
        );
    }

    /**
     * Update category.
     */
    public function update(
        Request $request,
        Category $category
    ): RedirectResponse {
        $validated = $this->validateCategory($request, $category);
        // Prevent a category from becoming its own ancestor.
        if (! empty($validated['parent_id'])) {
            $parentId = (int) $validated['parent_id'];
            if (
                $parentId === $category->id
                || in_array(
                    $parentId,
                    $this->getDescendantIds($category),
                    true
                )
            ) {
                throw ValidationException::withMessages([
                    'parent_id' => 'You cannot select this category as its own parent or descendant.',
                ]);
            }
        }

        $validated['seo_url'] = $this->generateUniqueSeoUrl(
            $validated['seo_url'] ?: $validated['category_name'],
            $category->id
        );

        if ($request->hasFile('thumb_image')) {
            $this->deleteImage($category->thumb_image);
            $validated['thumb_image'] = $request->file('thumb_image')
                ->store('categories/thumbnails', 'public');
        } else {
            unset($validated['thumb_image']);
        }

        if ($request->hasFile('large_image')) {
            $this->deleteImage($category->large_image);
            $validated['large_image'] = $request->file('large_image')
                ->store('categories/large', 'public');
        } else {
            unset($validated['large_image']);
        }

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Delete category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Avoid converting existing subcategories into main categories.
        if ($category->children()->exists()) {
            return redirect()
                ->route('admin.categories.index')
                ->with(
                    'error',
                    'This category has subcategories. Delete or move them first.'
                );
        }

        $this->deleteImage($category->thumb_image);
        $this->deleteImage($category->large_image);
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Validate category data.
     */
    private function validateCategory(
        Request $request,
        ?Category $category = null
    ): array {
        return $request->validate([
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id'),
                function ($attribute, $value, $fail) use ($category) {
                    if (
                        $category
                        && $value !== null
                        && (int) $value === (int) $category->id
                    ) {
                        $fail('A category cannot be its own parent.');
                    }
                },
            ],
            'category_name' => [
                'required',
                'string',
                'max:150',
            ],
            'summary' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'thumb_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'large_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'meta_title' => [
                'nullable',
                'string',
                'max:255',
            ],
            'meta_keywords' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'meta_description' => [
                'nullable',
                'string',
                'max:5000',
            ],
            'seo_url' => [
                'nullable',
                'string',
                'max:200',
                'regex:/^[A-Za-z0-9_-]+$/',
                Rule::unique('categories', 'seo_url')
                    ->ignore($category?->id),
            ],
            'status' => [
                'required',
                'integer',
                Rule::in([707, 505]),
            ],
            'sort_order' => [
                'required',
                'integer',
                'min:0',
                'max:4294967295',
            ],
        ]);
    }

    /**
     * Generate a unique SEO URL.
     *
     * Examples:
     * sarees
     * sarees-2
     * sarees-3
     */
    private function generateUniqueSeoUrl(
        string $value,
        ?int $ignoreId = null
    ): string {
        $baseSlug = Str::slug($value);
        if ($baseSlug === '') {
            $baseSlug = 'category';
        }
        $slug = $baseSlug;
        $counter = 2;
        while (
            Category::query()
                ->where('seo_url', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) => $query->where('id', '!=', $ignoreId)
                )
                ->exists()
        ) {
            $suffix = '-'.$counter;
            $slug = Str::limit(
                $baseSlug,
                200 - strlen($suffix),
                ''
            ).$suffix;
            $counter++;
        }

        return $slug;
    }

    /**
     * Flatten categories into parent-first options with readable paths.
     */
    private function buildCategoryOptions(Collection $categories): Collection
    {
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

        return $categoryOptions;
    }

    /**
     * Get all descendant IDs recursively.
     */
    private function getDescendantIds(Category $category): array
    {
        $ids = [];
        $category->loadMissing('children');
        foreach ($category->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge(
                $ids,
                $this->getDescendantIds($child)
            );
        }

        return $ids;
    }

    /**
     * Delete stored image if present.
     */
    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
