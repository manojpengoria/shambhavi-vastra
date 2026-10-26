{{--
    One payment method row with its settings form.
    $method: definition from config/payment_methods.php plus its "code".
    $paymentMethod: saved App\Models\PaymentMethod, or null when never activated.
    Until a method is activated for the first time only "Activate" shows; after that "Update" and "Deactivate".
--}}
@php
    $code = $method['code'];
    $logo = $method['logo'];
    $isActive = (bool) $paymentMethod?->is_active;
    $errorBag = $errors->getBag($code);
    // old() input belongs to this form only when it was the one submitted.
    $usesOldInput = old('payment_method') === $code;
    $status = session('payment_status');
    $statusMessage = ($status['code'] ?? null) === $code ? $status['message'] : null;
    $isOpen = $isActive || $errorBag->any() || $statusMessage !== null;
    $fieldValue = fn (string $name) => $usesOldInput ? old($name) : $paymentMethod?->fieldValue($name);
@endphp
<div
    id="payment-method-{{ $code }}"
    class="payment-method {{ $isActive ? 'is-active' : '' }} {{ $isOpen ? 'is-open' : '' }}"
    data-payment-method="{{ $code }}"
    data-state="{{ $isActive ? 'active' : 'inactive' }}"
>
    <div class="payment-method-head">
        <label class="payment-method-check" for="payment-method-toggle-{{ $code }}">
            <input
                type="checkbox"
                id="payment-method-toggle-{{ $code }}"
                class="toggle payment-check-input payment-method-toggle"
                aria-controls="payment-method-body-{{ $code }}"
                aria-expanded="{{ $isOpen ? 'true' : 'false' }}"
                @checked($isOpen)
            >
            <span class="sr-only">Show {{ $method['title'] }} settings</span>
        </label>

        <div class="payment-method-logo">
            @if ($logo['type'] === 'image')
                <img src="{{ asset($logo['path']) }}" alt="{{ $method['title'] }} logo" loading="lazy">
            @else
                <i class="fa {{ $logo['icon'] }}" style="color: {{ $logo['color'] }};" aria-hidden="true"></i>
            @endif
        </div>

        <div class="payment-method-info">
            <h3 class="payment-method-title">
                {{ $method['title'] }}
                <span class="label payment-method-status" @unless ($isActive) hidden @endunless>Active</span>
            </h3>
            <p class="payment-method-description">{{ $method['description'] }}</p>
        </div>
    </div>

    <div class="payment-method-body" id="payment-method-body-{{ $code }}" @unless ($isOpen) hidden @endunless>
        <form
            class="payment-method-form"
            method="POST"
            action="{{ route('admin.payment-options.save', $code) }}"
            data-payment-form
            data-method-title="{{ $method['title'] }}"
            novalidate
        >
            @csrf
            <input type="hidden" name="payment_method" value="{{ $code }}">

            <div class="row">
                @foreach ($method['fields'] as $field)
                    @php
                        $name = $field['name'];
                        $fieldId = "{$code}_{$name}";
                        $fieldError = $errorBag->first($name) ?: $errorBag->first("{$name}.*");
                        $savedSecret = $field['type'] === 'password' && filled($paymentMethod?->fieldValue($name));
                        $isRequired = ! empty($field['required']) && ! $savedSecret;
                        $columnClass = match ($field['width'] ?? 'half') {
                            'full' => 'col-xs-12',
                            'third' => 'col-md-4 col-sm-6 col-xs-12',
                            'quarter' => 'col-md-3 col-sm-6 col-xs-12',
                            'wide' => 'col-md-8 col-sm-12 col-xs-12',
                            default => 'col-md-6 col-sm-6 col-xs-12',
                        };
                    @endphp

                    @if ($field['type'] === 'heading')
                        <div class="col-xs-12">
                            <p class="payment-field-heading">{{ $field['label'] }}</p>
                        </div>
                    @elseif ($field['type'] === 'display_options')
                        @php
                            $selectedOptions = (array) ($fieldValue($name) ?? []);
                        @endphp
                        <div class="col-xs-12">
                            <div class="form-group {{ $fieldError ? 'has-error' : '' }}">
                                <label class="control-label">Tick the following payment options to display it at footer and checkout page of your website</label>
                                <div class="payment-display-options">
                                    @foreach ($field['options'] as $optionCode)
                                        @php
                                            $option = $displayOptions[$optionCode];
                                        @endphp
                                        <label class="payment-display-option" for="{{ $fieldId }}_{{ $optionCode }}">
                                            <input
                                                type="checkbox"
                                                id="{{ $fieldId }}_{{ $optionCode }}"
                                                class="toggle payment-check-input payment-check-input-sm"
                                                name="{{ $name }}[]"
                                                value="{{ $optionCode }}"
                                                @checked(in_array($optionCode, $selectedOptions, true))
                                            >
                                            <span class="payment-brand-chip" title="{{ $option['label'] }}">
                                                <img src="{{ asset($option['image']) }}" alt="{{ $option['label'] }}" loading="lazy">
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @if ($fieldError)
                                    <span class="help-block">{{ $fieldError }}</span>
                                @endif
                            </div>
                        </div>
                    @elseif ($field['type'] === 'show_icon')
                        <div class="col-xs-12">
                            <label class="payment-inline-check" for="{{ $fieldId }}">
                                <input type="hidden" name="{{ $name }}" value="0">
                                <input type="checkbox" id="{{ $fieldId }}" class="toggle payment-check-input payment-check-input-sm" name="{{ $name }}" value="1" @checked((bool) $fieldValue($name))>
                                <span>{{ $field['label'] }}</span>
                            </label>
                        </div>
                    @else
                        <div class="{{ $columnClass }}">
                            <div class="form-group {{ $fieldError ? 'has-error' : '' }}">
                                <label class="control-label" for="{{ $fieldId }}">
                                    {{ $field['label'] }}
                                    @if (! empty($field['required']))
                                        <span class="text-danger">*</span>
                                    @endif
                                    @if (! empty($field['help']))
                                        <span class="payment-field-help">{{ $field['help'] }}</span>
                                    @endif
                                </label>

                                @if ($field['type'] === 'textarea')
                                    <textarea
                                        id="{{ $fieldId }}"
                                        name="{{ $name }}"
                                        class="form-control"
                                        rows="3"
                                        maxlength="2000"
                                        @if ($isRequired) required data-required-message="{{ \App\Models\PaymentMethod::requiredMessage($field['label']) }}" @endif
                                    >{{ $fieldValue($name) }}</textarea>
                                @elseif ($field['type'] === 'mode')
                                    <select id="{{ $fieldId }}" name="{{ $name }}" class="form-control">
                                        <option value="test" @selected($fieldValue($name) !== 'live')>TEST</option>
                                        <option value="live" @selected($fieldValue($name) === 'live')>LIVE</option>
                                    </select>
                                @elseif ($field['type'] === 'amount')
                                    <input type="number" id="{{ $fieldId }}" name="{{ $name }}" class="form-control" value="{{ $fieldValue($name) }}" min="0" step="0.01" placeholder="0.00">
                                @elseif ($field['type'] === 'number')
                                    <input type="number" id="{{ $fieldId }}" name="{{ $name }}" class="form-control" value="{{ $fieldValue($name) }}" min="0" step="1">
                                @elseif ($field['type'] === 'password')
                                    <input
                                        type="password"
                                        id="{{ $fieldId }}"
                                        name="{{ $name }}"
                                        class="form-control"
                                        maxlength="500"
                                        autocomplete="new-password"
                                        placeholder="{{ $savedSecret ? 'Saved — leave blank to keep' : '' }}"
                                        @if ($isRequired) required data-required-message="{{ \App\Models\PaymentMethod::requiredMessage($field['label']) }}" @endif
                                    >
                                @else
                                    <input
                                        type="text"
                                        id="{{ $fieldId }}"
                                        name="{{ $name }}"
                                        class="form-control"
                                        value="{{ $fieldValue($name) }}"
                                        maxlength="255"
                                        autocomplete="off"
                                        @if ($isRequired) required data-required-message="{{ \App\Models\PaymentMethod::requiredMessage($field['label']) }}" @endif
                                    >
                                @endif
                                <span class="help-block payment-field-error" @unless ($fieldError) hidden @endunless>{{ $fieldError }}</span>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="payment-method-actions">
                <span class="payment-method-feedback" role="status" aria-live="polite">{{ $statusMessage }}</span>
                @if ($isActive)
                    <button type="submit" name="intent" value="deactivate" class="btn red-mint btn-outline" formnovalidate>
                        <i class="fa fa-power-off"></i> Deactivate
                    </button>
                    <button type="submit" name="intent" value="update" class="btn green-jungle">
                        <i class="fa fa-check"></i> Update
                    </button>
                @else
                    <button type="submit" name="intent" value="activate" class="btn green-jungle">
                        Activate
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>
