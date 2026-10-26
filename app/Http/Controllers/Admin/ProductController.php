<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ShippingClass;
use App\Models\ShippingPackage;
use App\Models\ShippingSetting;
use App\Models\VariantType;
use App\Models\VariantValue;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator as ValidationValidator;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Maximum number of variant option rows on the product form.
     */
    private const MAX_VARIANT_OPTIONS = 3;

    /**
     * Product Listing
     */
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['mainImage', 'categories:id,category_name'])
            ->withCount('variants')
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where(function ($query) use ($request) {
                    $search = $request->string('search')->trim()->toString();

                    $query->where('product_name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                })
            )
            ->when(
                $request->filled('category_id'),
                fn ($query) => $query->whereHas(
                    'categories',
                    fn ($query) => $query->where('categories.id', $request->integer('category_id'))
                )
            )
            ->when(
                $request->input('status') === 'published',
                fn ($query) => $query->where('is_draft', false)->where('status', Product::STATUS_VISIBLE)
            )
            ->when(
                $request->input('status') === 'draft',
                fn ($query) => $query->where('is_draft', true)
            )
            ->when(
                $request->input('status') === 'hidden',
                fn ($query) => $query->where('status', Product::STATUS_HIDDEN)
            )
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('category_name')->get(['id', 'category_name']);

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('admin.products.create', $this->formData());
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);

        DB::transaction(function () use ($request, $validated) {
            $product = Product::create($this->productAttributes($request, $validated));
            $this->syncRelations($product, $request, $validated);
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', $validated['action'] === 'draft' ? 'Product saved as draft.' : 'Product published successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Product $product): View
    {
        $product->load(['categories:id', 'images', 'variants.values']);

        return view('admin.products.edit', [
            'product' => $product,
            ...$this->formData($product),
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request, $product);

        DB::transaction(function () use ($request, $validated, $product) {
            $product->update($this->productAttributes($request, $validated, $product));
            $this->syncRelations($product, $request, $validated);
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', $validated['action'] === 'draft' ? 'Product saved as draft.' : 'Product updated successfully.');
    }

    /**
     * Remove the specified product, its variants and images.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $imagePaths = $product->images()->pluck('image_path');

        $product->delete();

        $imagePaths->each(fn (string $path) => $this->deleteImageFile($path));

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Data shared by the create and edit forms.
     *
     * @return array<string, mixed>
     */
    private function formData(?Product $product = null): array
    {
        $usedVariantValueIds = $product
            ? $product->variants->flatMap->values->pluck('id')->unique()->all()
            : [];

        $variantTypes = VariantType::query()
            ->where('status', 707)
            ->with([
                'values' => fn (HasMany $query) => $query
                    ->where(fn ($query) => $query->where('status', 707)->orWhereIn('id', $usedVariantValueIds))
                    ->select('id', 'variant_type_id', 'variant_value_name'),
            ])
            ->orderBy('sort_order')
            ->orderBy('variant_type_name')
            ->get(['id', 'variant_type_name']);

        $variantTypes->each(function (VariantType $variantType): void {
            $variantType->setAttribute(
                'values_json',
                $variantType->values
                    ->map(fn (VariantValue $value): array => [
                        'id' => (string) $value->id,
                        'text' => $value->variant_value_name,
                    ])
                    ->values()
                    ->toJson()
            );
        });

        $shippingClasses = ShippingClass::query()
            ->where(fn ($query) => $query->active()->orWhere('id', $product?->shipping_class_id))
            ->orderBy('name')
            ->get();

        $shippingPackages = ShippingPackage::query()
            ->where(fn ($query) => $query->active()->orWhere('id', $product?->shipping_package_id))
            ->orderBy('name')
            ->get();

        $imagesBySlot = $product ? $product->images->keyBy('sort_order') : collect();

        return [
            'categoryOptions' => $this->categoryOptions(),
            'selectedCategoryIds' => array_map(
                'strval',
                (array) old('category_ids', $product ? $product->categories->pluck('id')->all() : [])
            ),
            'variantTypes' => $variantTypes,
            'countries' => Country::enabled()->orderBy('country_name')->pluck('country_name'),
            'shippingClasses' => $shippingClasses,
            'shippingPackages' => $shippingPackages,
            'volumetricDivisor' => ShippingSetting::current()->volumetric_divisor ?: 5000,
            'defaultReturnDays' => ShippingSetting::current()->return_window_days,
            'imagesBySlot' => $imagesBySlot,
            'mainImageSlot' => (int) old('main_product_image', $imagesBySlot->firstWhere('is_main', true)?->sort_order ?? 1),
            'variantOptionRows' => old('variants', $product ? $this->variantOptionRows($product) : []),
            'initialVariants' => $this->initialVariants($product),
            'mainVariantKey' => old('main_variant_key', $product?->variants->firstWhere('is_main', true)?->combination_key),
        ];
    }

    /**
     * Active categories in hierarchy order with "Parent / Child" display names.
     *
     * @return SupportCollection<int, Category>
     */
    private function categoryOptions(): SupportCollection
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

        return $categoryOptions;
    }

    /**
     * Rebuild the variant option rows (type + selected values) from saved variants.
     *
     * @return array<int, array{enabled: string, variant_type_id: string, variant_value_ids: array<int, string>}>
     */
    private function variantOptionRows(Product $product): array
    {
        return $product->variants
            ->flatMap->values
            ->groupBy(fn (VariantValue $value) => $value->pivot->variant_type_id)
            ->map(fn (SupportCollection $values, int|string $variantTypeId) => [
                'enabled' => '1',
                'variant_type_id' => (string) $variantTypeId,
                'variant_value_ids' => $values->pluck('id')->unique()->map(fn ($id) => (string) $id)->values()->all(),
            ])
            ->take(self::MAX_VARIANT_OPTIONS)
            ->values()
            ->all();
    }

    /**
     * Generated variant rows keyed by combination key, used to prefill the variants table.
     *
     * @return array<string, array<string, mixed>>
     */
    private function initialVariants(?Product $product): array
    {
        $oldVariants = old('product_variants');

        if (is_array($oldVariants)) {
            return collect($oldVariants)
                ->filter(fn ($row) => is_array($row) && ! empty($row['key']))
                ->mapWithKeys(fn (array $row) => [
                    $this->normalizeCombinationKey((string) $row['key']) => [
                        'enabled' => ! empty($row['enabled']),
                        'product_price' => $row['product_price'] ?? '',
                        'discount_price' => $row['discount_price'] ?? '',
                        'net_price' => $row['net_price'] ?? '',
                        'sku' => $row['sku'] ?? '',
                        'barcode' => $row['barcode'] ?? '',
                        'weight' => $row['weight'] ?? '',
                        'quantity' => $row['quantity'] ?? '',
                    ],
                ])
                ->all();
        }

        if (! $product) {
            return [];
        }

        return $product->variants
            ->mapWithKeys(fn (ProductVariant $variant) => [
                $variant->combination_key => [
                    'enabled' => true,
                    'product_price' => $variant->product_price,
                    'discount_price' => $variant->discount_price,
                    'net_price' => $variant->net_price,
                    'sku' => $variant->sku ?? '',
                    'barcode' => $variant->barcode ?? '',
                    'weight' => $variant->weight ?? '',
                    'quantity' => $variant->quantity ?? '',
                ],
            ])
            ->all();
    }

    /**
     * Validate the product form.
     *
     * @return array<string, mixed>
     */
    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $enabledVariants = collect($request->input('product_variants', []))
            ->filter(fn ($row) => is_array($row) && ! empty($row['enabled']))
            ->values()
            ->all();

        $request->merge([
            'sku' => trim((string) $request->input('sku')),
            'seo_url' => Str::lower(trim((string) $request->input('seo_url'))),
            'enabled_product_variants' => $enabledVariants,
        ]);

        $imageSlots = implode(',', range(1, Product::MAX_IMAGES));
        $hasVariants = $request->boolean('has_variants');
        $tracksInventory = $request->boolean('track_inventory');
        $productId = $product?->id;
        $skuFormat = 'regex:/^[A-Za-z0-9\-_\/.]+$/';
        $barcodeFormat = 'regex:/^[A-Za-z0-9\-]{6,50}$/';

        $rules = [
            'action' => ['required', Rule::in(['draft', 'publish'])],

            // Categories & visibility
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['integer', 'distinct', Rule::exists('categories', 'id')->where('status', 707)],
            'is_visible' => ['nullable', 'boolean'],
            'publish_at' => ['nullable', 'date'],
            'visible_after_login' => ['nullable', 'boolean'],

            // Details
            'product_name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'short_summary_brand' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string', 'max:2000'],
            'description' => ['nullable', 'string', 'max:100000'],
            'hsn_sac_code' => ['nullable', 'regex:/^[0-9]{4,8}$/'],
            'sku' => [
                'required',
                'string',
                'max:100',
                $skuFormat,
                Rule::unique('products', 'sku')->ignore($product),
                Rule::unique('product_variants', 'sku')->where(
                    fn ($query) => $query->when($productId, fn ($query) => $query->where('product_id', '!=', $productId))
                ),
            ],
            'barcode' => ['nullable', $barcodeFormat],
            'origin_country' => ['nullable', 'string', Rule::exists('countries', 'country_name')],
            'track_inventory' => ['nullable', 'boolean'],
            'stock_quantity' => [
                'exclude_if:has_variants,1',
                Rule::requiredIf($tracksInventory),
                'nullable',
                'integer',
                'min:0',
                'max:99999999',
            ],

            // Images
            'product_images' => ['nullable', "array:{$imageSlots}"],
            'product_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'existing_image_ids' => ['nullable', "array:{$imageSlots}"],
            'existing_image_ids.*' => [
                'integer',
                'distinct',
                Rule::exists('product_images', 'id')->where('product_id', $productId ?? 0),
            ],
            'main_product_image' => ['nullable', 'integer', 'between:1,'.Product::MAX_IMAGES],
            'youtube_video' => ['nullable', 'regex:/^[A-Za-z0-9_\-]{11}$/'],

            // Pricing
            'selling_price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'discount' => ['nullable', 'numeric', 'min:0', 'lte:selling_price'],
            'moq' => ['required', 'integer', 'min:1', 'max:65535'],
            'purchase_cost' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'taxable' => ['nullable', 'boolean'],
            'tax_rate' => ['exclude_unless:taxable,1', 'required', 'numeric', 'min:0', 'max:100'],

            // Variants
            'has_variants' => ['nullable', 'boolean'],
            'enabled_product_variants' => ['exclude_unless:has_variants,1', 'required', 'array', 'max:500'],
            'enabled_product_variants.*.key' => [
                'exclude_unless:has_variants,1',
                'required',
                'string',
                'max:255',
                'regex:/^\d+:\d+(\|\d+:\d+)*$/',
            ],
            'enabled_product_variants.*.product_price' => ['exclude_unless:has_variants,1', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'enabled_product_variants.*.discount_price' => [
                'exclude_unless:has_variants,1',
                'nullable',
                'numeric',
                'min:0',
                'lte:enabled_product_variants.*.product_price',
            ],
            'enabled_product_variants.*.sku' => [
                'exclude_unless:has_variants,1',
                'nullable',
                'string',
                'max:100',
                $skuFormat,
                'distinct:ignore_case',
                'different:sku',
                Rule::unique('products', 'sku'),
                Rule::unique('product_variants', 'sku')->where(
                    fn ($query) => $query->when($productId, fn ($query) => $query->where('product_id', '!=', $productId))
                ),
            ],
            'enabled_product_variants.*.barcode' => ['exclude_unless:has_variants,1', 'nullable', $barcodeFormat],
            'enabled_product_variants.*.weight' => ['exclude_unless:has_variants,1', 'nullable', 'numeric', 'gt:0', 'max:99999.999'],
            'enabled_product_variants.*.quantity' => [
                'exclude_unless:has_variants,1',
                Rule::requiredIf($hasVariants && $tracksInventory),
                'nullable',
                'integer',
                'min:0',
                'max:99999999',
            ],
            'main_variant_key' => ['exclude_unless:has_variants,1', 'required', 'string'],

            // Shipping
            'weight' => ['required', 'numeric', 'gt:0', 'max:99999.999'],
            'length' => ['nullable', 'required_with:breadth,height', 'numeric', 'gt:0', 'max:999999.99'],
            'breadth' => ['nullable', 'required_with:length,height', 'numeric', 'gt:0', 'max:999999.99'],
            'height' => ['nullable', 'required_with:length,breadth', 'numeric', 'gt:0', 'max:999999.99'],
            'shipping_class_id' => [
                'nullable',
                'integer',
                Rule::exists('shipping_classes', 'id')->where(
                    fn ($query) => $query->where('is_active', true)
                        ->when($product?->shipping_class_id, fn ($query, $currentId) => $query->orWhere('id', $currentId))
                ),
            ],
            'shipping_package_id' => [
                'nullable',
                'integer',
                Rule::exists('shipping_packages', 'id')->where(
                    fn ($query) => $query->where('is_active', true)
                        ->when($product?->shipping_package_id, fn ($query, $currentId) => $query->orWhere('id', $currentId))
                ),
            ],
            'free_shipping' => ['nullable', 'boolean'],
            'cod_available' => ['nullable', 'boolean'],
            'shipping_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'out_of_stock_days' => [
                'nullable',
                'integer',
                'min:0',
                'max:365',
                Rule::when($request->filled('shipping_days'), 'gte:shipping_days'),
            ],
            'is_returnable' => ['nullable', 'boolean'],
            'return_days' => ['exclude_unless:is_returnable,1', 'nullable', 'integer', 'min:1', 'max:365'],

            // Labels
            'label' => ['nullable', Rule::in(array_keys(Product::LABELS))],
            'custom_label' => ['exclude_unless:label,custom', 'required', 'string', 'max:50'],

            // SEO
            'custom_seo_url' => ['nullable', 'boolean'],
            'seo_url' => [
                'exclude_unless:custom_seo_url,1',
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('products', 'seo_url')->ignore($product),
            ],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:1000'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
        ];

        $messages = [
            'category_ids.required' => 'Select at least one category.',
            'hsn_sac_code.regex' => 'The HSN / SAC code must be 4 to 8 digits.',
            'sku.regex' => 'The SKU may only contain letters, numbers, dashes, underscores, dots and slashes.',
            'sku.unique' => 'This SKU is already used by another product or variant.',
            'barcode.regex' => 'The barcode must be 6 to 50 letters, numbers or dashes.',
            'stock_quantity.required' => 'Enter the stock quantity when inventory tracking is enabled.',
            'product_images.*.image' => 'Each product image must be an image file.',
            'product_images.*.mimes' => 'Product images must be JPG, PNG or WEBP files.',
            'product_images.*.max' => 'Each product image may not be larger than 2 MB.',
            'youtube_video.regex' => 'Enter the 11 character YouTube video code (the part after /embed/).',
            'discount.lte' => 'The discount may not be greater than the selling price.',
            'tax_rate.required' => 'Enter the tax rate for a taxable product.',
            'enabled_product_variants.required' => 'Generate the variants and keep at least one variant selected.',
            'enabled_product_variants.*.product_price.required' => 'Every selected variant needs a product price.',
            'enabled_product_variants.*.discount_price.lte' => 'A variant discount price may not be greater than its product price.',
            'enabled_product_variants.*.sku.distinct' => 'Variant SKUs must be unique.',
            'enabled_product_variants.*.sku.different' => 'A variant SKU must be different from the product SKU.',
            'enabled_product_variants.*.sku.unique' => 'A variant SKU is already used by another product or variant.',
            'enabled_product_variants.*.quantity.required' => 'Enter the quantity for every variant when inventory tracking is enabled.',
            'main_variant_key.required' => 'Select the main variant.',
            'length.required_with' => 'Enter length, breadth and height together.',
            'breadth.required_with' => 'Enter length, breadth and height together.',
            'height.required_with' => 'Enter length, breadth and height together.',
            'out_of_stock_days.gte' => 'Out of stock shipment days may not be less than the normal shipment days.',
            'custom_label.required' => 'Enter the custom label text.',
            'seo_url.regex' => 'The SEO URL may only contain lowercase letters, numbers and single dashes.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $validator->after(function (ValidationValidator $validator) use ($request, $hasVariants, $enabledVariants) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $this->validateImageCount($validator, $request);

            if ($hasVariants) {
                $this->validateVariantCombinations($validator, $request, $enabledVariants);
            }
        });

        return $validator->validate();
    }

    /**
     * A published product needs at least one image.
     */
    private function validateImageCount(ValidationValidator $validator, Request $request): void
    {
        $imageCount = count($request->file('product_images', []))
            + count(array_filter((array) $request->input('existing_image_ids', [])));

        if ($request->input('action') === 'publish' && $imageCount === 0) {
            $validator->errors()->add('product_images', 'Upload at least one product image before publishing.');
        }
    }

    /**
     * Check that every combination uses valid, consistent variant types and values.
     *
     * @param  array<int, array<string, mixed>>  $enabledVariants
     */
    private function validateVariantCombinations(ValidationValidator $validator, Request $request, array $enabledVariants): void
    {
        $combinations = collect($enabledVariants)->map(fn (array $row) => $this->parseCombinationKey((string) $row['key']));

        $valueTypeIds = VariantValue::query()
            ->whereIn('id', $combinations->flatten()->unique()->all())
            ->pluck('variant_type_id', 'id');

        $typeSignatures = collect();

        foreach ($combinations as $pairs) {
            $typeIds = array_keys($pairs);

            if (count($typeIds) > self::MAX_VARIANT_OPTIONS) {
                $validator->errors()->add('product_variants', 'A product can use at most '.self::MAX_VARIANT_OPTIONS.' variant options.');

                return;
            }

            foreach ($pairs as $typeId => $valueId) {
                if ((int) ($valueTypeIds[$valueId] ?? 0) !== $typeId) {
                    $validator->errors()->add('product_variants', 'One or more variant values are invalid. Please regenerate the variants.');

                    return;
                }
            }

            $typeSignatures->push(implode(',', $typeIds));
        }

        if ($typeSignatures->unique()->count() > 1) {
            $validator->errors()->add('product_variants', 'All variants must use the same variant options. Please regenerate the variants.');

            return;
        }

        $normalizedKeys = collect($enabledVariants)->map(fn (array $row) => $this->normalizeCombinationKey((string) $row['key']));

        if ($normalizedKeys->duplicates()->isNotEmpty()) {
            $validator->errors()->add('product_variants', 'Each variant combination may only appear once.');

            return;
        }

        if (! $normalizedKeys->contains($this->normalizeCombinationKey((string) $request->input('main_variant_key')))) {
            $validator->errors()->add('main_variant_key', 'The main variant must be one of the selected variants.');
        }
    }

    /**
     * Parse "typeId:valueId|typeId:valueId" into [typeId => valueId], sorted by type.
     *
     * @return array<int, int>
     */
    private function parseCombinationKey(string $key): array
    {
        $pairs = [];

        foreach (explode('|', $key) as $pair) {
            [$typeId, $valueId] = array_map('intval', explode(':', $pair) + [1 => 0]);
            $pairs[$typeId] = $valueId;
        }

        ksort($pairs);

        return $pairs;
    }

    /**
     * Normalize a combination key so pair order does not matter.
     */
    private function normalizeCombinationKey(string $key): string
    {
        return collect($this->parseCombinationKey($key))
            ->map(fn (int $valueId, int $typeId) => "{$typeId}:{$valueId}")
            ->implode('|');
    }

    /**
     * Map validated input to product columns.
     *
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function productAttributes(Request $request, array $validated, ?Product $product = null): array
    {
        $sellingPrice = (float) $validated['selling_price'];
        $discount = (float) ($validated['discount'] ?? 0);
        $hasVariants = $request->boolean('has_variants');
        $tracksInventory = $request->boolean('track_inventory');
        $isReturnable = $request->boolean('is_returnable');
        $label = $validated['label'] ?? null;

        return [
            'product_name' => $validated['product_name'],
            'sku' => $validated['sku'],
            'barcode' => $validated['barcode'] ?? null,
            'hsn_sac_code' => $validated['hsn_sac_code'] ?? null,
            'short_summary_brand' => $validated['short_summary_brand'] ?? null,
            'summary' => $validated['summary'] ?? null,
            'description' => $validated['description'] ?? null,
            'origin_country' => $validated['origin_country'] ?? null,
            'youtube_video' => $validated['youtube_video'] ?? null,
            'selling_price' => $sellingPrice,
            'discount' => $discount,
            'net_price' => max(0, $sellingPrice - $discount),
            'purchase_cost' => $validated['purchase_cost'] ?? null,
            'moq' => $validated['moq'],
            'taxable' => $request->boolean('taxable'),
            'tax_rate' => $validated['tax_rate'] ?? null,
            'track_inventory' => $tracksInventory,
            'stock_quantity' => $tracksInventory && ! $hasVariants ? $validated['stock_quantity'] : null,
            'has_variants' => $hasVariants,
            'weight' => $validated['weight'],
            'length' => $validated['length'] ?? null,
            'breadth' => $validated['breadth'] ?? null,
            'height' => $validated['height'] ?? null,
            'shipping_class_id' => $validated['shipping_class_id'] ?? null,
            'shipping_package_id' => $validated['shipping_package_id'] ?? null,
            'free_shipping' => $request->boolean('free_shipping'),
            'cod_available' => $request->boolean('cod_available'),
            'shipping_days' => $validated['shipping_days'] ?? null,
            'out_of_stock_days' => $validated['out_of_stock_days'] ?? null,
            'is_returnable' => $isReturnable,
            'return_days' => $isReturnable ? ($validated['return_days'] ?? null) : null,
            'label' => $label,
            'custom_label' => $label === 'custom' ? $validated['custom_label'] : null,
            'seo_url' => $request->boolean('custom_seo_url')
                ? $validated['seo_url']
                : $this->generateUniqueSeoUrl($validated['product_name'], $product?->id),
            'meta_title' => $validated['meta_title'] ?? $validated['product_name'],
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'status' => $request->boolean('is_visible') ? Product::STATUS_VISIBLE : Product::STATUS_HIDDEN,
            'is_draft' => $validated['action'] === 'draft',
            'publish_at' => $validated['publish_at'] ?? null,
            'visible_after_login' => $request->boolean('visible_after_login'),
            'sort_order' => $validated['sort_order'] ?? 0,
        ];
    }

    /**
     * Sync categories, images and variants of a saved product.
     *
     * @param  array<string, mixed>  $validated
     */
    private function syncRelations(Product $product, Request $request, array $validated): void
    {
        $product->categories()->sync($validated['category_ids']);
        $this->syncImages($product, $request, $validated);
        $this->syncVariants($product, $validated);
    }

    /**
     * Keep the retained images, store new uploads by slot and mark the main image.
     *
     * @param  array<string, mixed>  $validated
     */
    private function syncImages(Product $product, Request $request, array $validated): void
    {
        /** @var array<int, UploadedFile> $uploadedImages */
        $uploadedImages = $request->file('product_images', []);

        // A new upload in a slot replaces the image previously kept in that slot.
        $keptImageIds = collect($validated['existing_image_ids'] ?? [])
            ->filter()
            ->except(array_keys($uploadedImages));

        $product->images()
            ->whereNotIn('id', $keptImageIds->values()->all())
            ->get()
            ->each(function (ProductImage $image) {
                $this->deleteImageFile($image->image_path);
                $image->delete();
            });

        foreach ($keptImageIds as $slot => $imageId) {
            ProductImage::whereKey($imageId)->update(['sort_order' => $slot]);
        }

        foreach ($uploadedImages as $slot => $uploadedImage) {
            $product->images()->create([
                'image_path' => $uploadedImage->store("products/{$product->id}", 'public'),
                'sort_order' => $slot,
            ]);
        }

        $images = $product->images()->get();
        $mainImage = $images->firstWhere('sort_order', (int) ($validated['main_product_image'] ?? 0)) ?? $images->first();

        // Plain queries: a loaded model already flagged is_main would skip its save as "not dirty".
        $product->images()->update(['is_main' => false]);

        if ($mainImage) {
            ProductImage::whereKey($mainImage->id)->update(['is_main' => true]);
        }
    }

    /**
     * Create, update or remove variants by their combination key.
     *
     * @param  array<string, mixed>  $validated
     */
    private function syncVariants(Product $product, array $validated): void
    {
        if (empty($validated['has_variants'])) {
            $product->variants()->delete();

            return;
        }

        $rows = collect($validated['enabled_product_variants'])
            ->map(fn (array $row) => [...$row, 'key' => $this->normalizeCombinationKey($row['key'])]);
        $mainVariantKey = $this->normalizeCombinationKey($validated['main_variant_key']);

        $product->variants()->whereNotIn('combination_key', $rows->pluck('key')->all())->delete();

        // Free existing SKUs first so variants can swap SKUs within one save.
        $product->variants()->update(['sku' => null]);

        $existingVariants = $product->variants()->get()->keyBy('combination_key');

        foreach ($rows->values() as $index => $row) {
            $productPrice = (float) $row['product_price'];
            $discountPrice = (float) ($row['discount_price'] ?? 0);

            $variant = $existingVariants->get($row['key']) ?? new ProductVariant(['product_id' => $product->id]);
            $variant->fill([
                'combination_key' => $row['key'],
                'sku' => filled($row['sku'] ?? null) ? $row['sku'] : null,
                'barcode' => filled($row['barcode'] ?? null) ? $row['barcode'] : null,
                'product_price' => $productPrice,
                'discount_price' => $discountPrice,
                'net_price' => max(0, $productPrice - $discountPrice),
                'weight' => filled($row['weight'] ?? null) ? $row['weight'] : null,
                'quantity' => filled($row['quantity'] ?? null) ? $row['quantity'] : null,
                'is_main' => $row['key'] === $mainVariantKey,
                'sort_order' => $index,
            ])->save();

            $variant->values()->sync(
                collect($this->parseCombinationKey($row['key']))
                    ->mapWithKeys(fn (int $valueId, int $typeId) => [$valueId => ['variant_type_id' => $typeId]])
                    ->all()
            );
        }
    }

    /**
     * Generate a unique SEO URL from the product name.
     */
    private function generateUniqueSeoUrl(string $productName, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($productName) ?: 'product';
        $slug = $baseSlug;
        $counter = 2;

        while (
            Product::query()
                ->where('seo_url', $slug)
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Delete a stored product image file if present.
     */
    private function deleteImageFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
