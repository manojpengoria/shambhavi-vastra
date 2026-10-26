<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentOptionController extends Controller
{
    /**
     * Field types that are layout only and never submitted.
     *
     * @var array<int, string>
     */
    private const NON_INPUT_TYPES = ['heading'];

    /**
     * Show every payment method from the registry with its saved settings.
     */
    public function index(): View
    {
        $paymentMethods = PaymentMethod::all()->keyBy('code');

        $groups = collect(config('payment_methods.groups'))
            ->map(fn (string $title, string $group) => [
                'title' => $title,
                'methods' => collect(config('payment_methods.methods'))
                    ->filter(fn (array $definition) => $definition['group'] === $group)
                    ->map(fn (array $definition, string $code) => [...$definition, 'code' => $code]),
            ]);

        return view('admin.payment-options.index', [
            'groups' => $groups,
            'paymentMethods' => $paymentMethods,
            'displayOptions' => config('payment_methods.display_options'),
        ]);
    }

    /**
     * Activate (first time creates the record), update or deactivate a payment method.
     */
    public function save(Request $request, string $code): RedirectResponse
    {
        $definition = config("payment_methods.methods.{$code}");
        abort_if($definition === null, 404);

        $request->validate([
            'intent' => ['required', Rule::in(['activate', 'update', 'deactivate'])],
        ]);

        $paymentMethod = PaymentMethod::firstWhere('code', $code);

        if ($request->input('intent') === 'deactivate') {
            abort_if($paymentMethod === null, 404);
            $paymentMethod->update(['is_active' => false]);

            return $this->redirectWithStatus($code, "{$definition['title']} deactivated.");
        }

        $validated = $request->validateWithBag(
            $code,
            $this->rules($definition, $paymentMethod, $request),
            $this->messages($definition)
        );

        $paymentMethod ??= new PaymentMethod(['code' => $code]);
        $wasActive = $paymentMethod->exists && $paymentMethod->is_active;

        $paymentMethod->fill([
            ...$this->columnValues($definition, $validated, $request),
            'settings' => $this->settingsValues($definition, $validated, $paymentMethod),
            'is_active' => true,
        ]);
        $paymentMethod->activated_at ??= now();
        $paymentMethod->save();

        return $this->redirectWithStatus(
            $code,
            $definition['title'].($wasActive ? ' updated.' : ' activated.')
        );
    }

    /**
     * Build validation rules from the field definitions.
     *
     * @param  array<string, mixed>  $definition
     * @return array<string, array<int, mixed>>
     */
    private function rules(array $definition, ?PaymentMethod $paymentMethod, Request $request): array
    {
        $rules = [];

        foreach ($this->inputFields($definition) as $field) {
            $name = $field['name'];
            $isRequired = ! empty($field['required'])
                // A saved secret may be left blank to keep it.
                && ! ($field['type'] === 'password' && filled($paymentMethod?->fieldValue($name)));
            $presence = $isRequired ? 'required' : 'nullable';

            $rules[$name] = match ($field['type']) {
                'password' => [$presence, 'string', 'max:500'],
                'textarea' => [$presence, 'string', 'max:2000'],
                'amount' => [$presence, 'numeric', 'min:0', 'max:99999999.99'],
                'number' => [$presence, 'integer', 'min:0', 'max:65535'],
                'mode' => ['required', Rule::in(['test', 'live'])],
                'display_options' => ['nullable', 'array'],
                'show_icon' => ['nullable', 'boolean'],
                default => [$presence, 'string', 'max:255'],
            };

            if ($field['type'] === 'display_options') {
                $rules["{$name}.*"] = ['string', 'distinct', Rule::in($field['options'])];
            }
        }

        if (isset($rules['max_order_amount'], $rules['min_order_amount'])) {
            $rules['max_order_amount'][] = Rule::when($request->filled('min_order_amount'), 'gte:min_order_amount');
        }

        return $rules;
    }

    /**
     * "Please enter ..." messages matching the inline client-side validation.
     *
     * @param  array<string, mixed>  $definition
     * @return array<string, string>
     */
    private function messages(array $definition): array
    {
        $messages = ['max_order_amount.gte' => 'The maximum order amount must be greater than or equal to the minimum order amount.'];

        foreach ($this->inputFields($definition) as $field) {
            $messages["{$field['name']}.required"] = PaymentMethod::requiredMessage($field['label'] ?? $field['name']).'.';
        }

        return $messages;
    }

    /**
     * Values for the shared columns.
     *
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function columnValues(array $definition, array $validated, Request $request): array
    {
        $fieldNames = collect($this->inputFields($definition))->pluck('name')->all();
        $values = [];

        foreach (PaymentMethod::COLUMN_FIELDS as $column) {
            if (! in_array($column, $fieldNames, true)) {
                continue;
            }

            $values[$column] = match ($column) {
                'show_icon' => $request->boolean('show_icon'),
                'display_options' => array_values($validated['display_options'] ?? []),
                'sort_order' => $validated['sort_order'] ?? 0,
                default => $validated[$column] ?? null,
            };
        }

        return $values;
    }

    /**
     * Method specific values for the encrypted settings column.
     * Blank secret fields keep their saved value.
     *
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function settingsValues(array $definition, array $validated, PaymentMethod $paymentMethod): array
    {
        $settings = [];

        foreach ($this->inputFields($definition) as $field) {
            $name = $field['name'];

            if (in_array($name, PaymentMethod::COLUMN_FIELDS, true)) {
                continue;
            }

            $settings[$name] = $field['type'] === 'password' && blank($validated[$name] ?? null)
                ? $paymentMethod->fieldValue($name)
                : ($validated[$name] ?? null);
        }

        return $settings;
    }

    /**
     * Fields that are submitted with the form.
     *
     * @param  array<string, mixed>  $definition
     * @return array<int, array<string, mixed>>
     */
    private function inputFields(array $definition): array
    {
        return Arr::where(
            $definition['fields'],
            fn (array $field) => ! in_array($field['type'], self::NON_INPUT_TYPES, true)
        );
    }

    /**
     * Back to the page, scrolled to the method, with its status message.
     */
    private function redirectWithStatus(string $code, string $message): RedirectResponse
    {
        return redirect()
            ->to(route('admin.payment-options.index').'#payment-method-'.$code)
            ->with('payment_status', ['code' => $code, 'message' => $message]);
    }
}
