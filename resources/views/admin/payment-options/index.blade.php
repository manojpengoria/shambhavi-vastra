@extends('admin.layouts.app')

@section('title', 'Payment Options | Admin')

@section('content')
<div class="page-content">
    <div class="page-head">
        <div class="page-title">
            <h1>Payment Options <small>Choose how your customers can pay.</small></h1>
        </div>
    </div>

    <ul class="page-breadcrumb breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li><span>Payment Options</span></li>
    </ul>

    <div class="payment-options-page">
        @foreach ($groups as $group => $section)
            @continue($section['methods']->isEmpty())
            <section class="payment-section">
                <h2 class="payment-section-title">{{ $section['title'] }}</h2>
                <div class="payment-card">
                    @if ($group === 'gateway')
                        <button
                            type="button"
                            class="payment-providers-toggle"
                            aria-expanded="true"
                            aria-controls="payment-gateway-list"
                        >
                            <span class="payment-providers-title">View all other payment providers</span>
                            <span class="payment-providers-list">
                                EBS, CCAvenue, PayU Payment, HDFC, Razorpay, ICICI, ICICI Net Banking, Atom Paynetz, iSure, Instamojo Payment Gateway
                                <i class="fa fa-level-down" aria-hidden="true"></i>
                            </span>
                        </button>
                    @endif

                    <div @if ($group === 'gateway') id="payment-gateway-list" class="payment-gateway-list" @endif>
                        @foreach ($section['methods'] as $method)
                            @include('admin.payment-options._method', [
                                'method' => $method,
                                'paymentMethod' => $paymentMethods->get($method['code']),
                            ])
                        @endforeach
                    </div>
                </div>
            </section>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
    <style>
        .payment-options-page {
            padding-top: 10px;
        }

        .payment-section {
            display: flex;
            align-items: flex-start;
            gap: 0px;
            margin-bottom: 40px;
        }

        .payment-section-title {
            flex: 0 0 18%;
            margin: 25px 0 0;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .payment-card {
            position: relative;
            flex: 1 1 auto;
            min-width: 0;
            padding: 10px 30px 20px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        /* Pointer arrow towards the section title */
        .payment-card::before {
            content: "";
            position: absolute;
            top: 24px;
            left: -14px;
            border-top: 14px solid transparent;
            border-bottom: 14px solid transparent;
            border-right: 14px solid #fff;
        }

        .payment-method + .payment-method {
            border-top: 1px dashed #d5d5d5;
        }

        .payment-method-head {
            display: grid;
            grid-template-columns: 56px 190px minmax(0, 1fr);
            align-items: center;
            padding: 22px 0;
        }

        .payment-method-check {
            margin: 0;
            cursor: pointer;
        }

        .payment-method-logo {
            display: flex;
            align-items: center;
            min-height: 70px;
            padding: 0 20px;
            border-left: 1px dashed #e1e1e1;
        }

        .payment-method-logo img {
            max-width: 150px;
            max-height: 60px;
        }

        .payment-method-logo .fa {
            font-size: 44px;
        }

        .payment-method-info {
            min-width: 0;
            padding-left: 10px;
        }

        .payment-method-title {
            margin: 0 0 6px;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        /* "toggle" class keeps Metronic's uniform plugin away from these custom checkboxes */
        .payment-method-status {
            background-color: #5cb85c;
            margin-left: 8px;
            font-size: 11px;
            vertical-align: middle;
        }

        .payment-method-description {
            margin: 0;
            font-size: 12px;
            color: #777;
        }

        .payment-method-body {
            padding: 0 0 22px;
        }

        .payment-method-body[hidden] {
            display: none;
        }

        .payment-method-body .control-label,
        .payment-field-heading {
            font-weight: 600;
            color: #666;
        }

        .payment-field-heading {
            margin: 0 0 10px;
            min-height: 1px;
        }

        .payment-field-error {
            margin-bottom: 0;
        }

        .payment-field-error[hidden] {
            display: none;
        }

        .payment-field-help {
            font-weight: 400;
            color: #999;
        }

        /* Square green checkboxes, as in the design */
        .payment-check-input {
            appearance: none;
            -webkit-appearance: none;
            position: relative;
            width: 20px;
            height: 20px;
            margin: 0;
            border: 3px solid #ccc;
            border-radius: 2px;
            background: #fff;
            cursor: pointer;
            vertical-align: middle;
            transition: border-color 0.15s ease;
        }

        .payment-check-input:hover,
        .payment-check-input:focus,
        .payment-check-input:checked {
            border-color: #5cb85c;
            outline: none;
        }

        .payment-check-input:checked::after {
            content: "";
            position: absolute;
            left: 4px;
            top: 0px;
            width: 8px;
            height: 12px;
            border: solid #5cb85c;
            border-width: 0 4px 4px 0;
            transform: rotate(45deg);
        }

        .payment-check-input-sm {
            width: 24px;
            height: 24px;
            border-width: 2px;
        }

        .payment-check-input-sm:checked::after {
            left: 6px;
            top: 1px;
            width: 7px;
            height: 13px;
            border-width: 0 3px 3px 0;
        }

        .payment-display-options {
            display: flex;
            flex-wrap: wrap;
            gap: 14px 22px;
        }

        .payment-display-option {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            cursor: pointer;
            font-weight: 400;
        }

        .payment-brand-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 96px;
            height: 50px;
            padding: 6px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #fff;
        }

        .payment-brand-chip img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .payment-inline-check {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin: 5px 0 20px;
            font-weight: 600;
            color: #666;
            cursor: pointer;
        }

        .payment-method-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 10px;
        }

        /* .btn / .label set display, so [hidden] needs to win explicitly */
        .payment-method-actions [hidden],
        .payment-method-status[hidden] {
            display: none !important;
        }

        .payment-method-feedback {
            margin-right: auto;
            color: #3c763d;
            font-weight: 600;
        }

        .payment-providers-toggle {
            display: block;
            width: 100%;
            padding: 22px 0;
            border: 0;
            border-bottom: 1px dashed #d5d5d5;
            background: none;
            text-align: left;
        }

        .payment-providers-title {
            display: block;
            margin-bottom: 4px;
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .payment-providers-list {
            font-size: 14px;
            color: #888;
        }

        .payment-providers-toggle[aria-expanded="false"] .fa {
            transform: rotate(180deg);
        }

        .payment-gateway-list {
            padding-left: 40px;
        }

        .payment-gateway-list[hidden] {
            display: none;
        }

        @media (max-width: 991px) {
            .payment-section {
                flex-direction: column;
                gap: 12px;
            }

            .payment-section-title {
                flex-basis: auto;
                margin-top: 0;
            }

            .payment-card::before {
                display: none;
            }

            .payment-gateway-list {
                padding-left: 0;
            }
        }

        @media (max-width: 767px) {
            .payment-card {
                padding: 5px 16px 15px;
            }

            .payment-method-head {
                grid-template-columns: 44px minmax(0, 1fr);
                row-gap: 10px;
                padding: 16px 0;
            }

            .payment-method-logo {
                min-height: 0;
                padding: 0;
                border-left: 0;
            }

            .payment-method-info {
                grid-column: 1 / -1;
                padding-left: 0;
            }

            .payment-method-title,
            .payment-providers-title {
                font-size: 18px;
            }

            .payment-method-description {
                font-size: 14px;
            }

            .payment-method-logo img {
                max-width: 130px;
                max-height: 44px;
            }

            .payment-method-actions {
                flex-direction: column-reverse;
                align-items: stretch;
            }

            .payment-method-feedback {
                margin-right: 0;
                text-align: center;
            }

            .payment-method-actions .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-payment-method]').forEach(function (method) {
                const toggle = method.querySelector('.payment-method-toggle');
                const body = method.querySelector('.payment-method-body');

                toggle.addEventListener('change', function () {
                    body.hidden = !this.checked;
                    method.classList.toggle('is-open', this.checked);
                    this.setAttribute('aria-expanded', String(this.checked));
                });
            });

            /*
             * Inline validation: every invalid field shows its own message below the input
             * (e.g. "Please enter payment instructions") instead of the browser's single tooltip.
             */
            const showFieldError = function (field, message) {
                const formGroup = field.closest('.form-group');
                const error = formGroup.querySelector('.payment-field-error');

                formGroup.classList.toggle('has-error', message !== '');
                error.textContent = message;
                error.hidden = message === '';
            };

            const fieldErrorMessage = function (field) {
                const isBlank = field.value.trim() === '';

                if (field.required && isBlank) {
                    return field.dataset.requiredMessage || 'This field is required.';
                }

                return field.checkValidity() ? '' : field.validationMessage;
            };

            const validateForm = function (form) {
                let firstInvalidField = null;

                form.querySelectorAll('.form-control').forEach(function (field) {
                    const message = fieldErrorMessage(field);
                    showFieldError(field, message);

                    if (message !== '' && !firstInvalidField) {
                        firstInvalidField = field;
                    }
                });

                if (firstInvalidField) {
                    firstInvalidField.focus();
                }

                return firstInvalidField === null;
            };

            document.querySelectorAll('[data-payment-form] .form-control').forEach(function (field) {
                ['input', 'change'].forEach(function (eventName) {
                    field.addEventListener(eventName, function () {
                        if (this.closest('.form-group').classList.contains('has-error')) {
                            showFieldError(this, fieldErrorMessage(this));
                        }
                    });
                });
            });

            document.querySelectorAll('[data-payment-form]').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    const intent = event.submitter ? event.submitter.value : 'update';

                    if (intent === 'deactivate') {
                        if (!window.confirm('Deactivate ' + form.dataset.methodTitle + '? Customers will no longer see it at checkout.')) {
                            event.preventDefault();
                        }
                        return;
                    }

                    if (!validateForm(form)) {
                        event.preventDefault();
                    }
                });
            });

            const providersToggle = document.querySelector('.payment-providers-toggle');
            const gatewayList = document.getElementById('payment-gateway-list');

            providersToggle?.addEventListener('click', function () {
                gatewayList.hidden = !gatewayList.hidden;
                this.setAttribute('aria-expanded', String(!gatewayList.hidden));
            });
        });
    </script>
@endpush
