<?php

/*
|--------------------------------------------------------------------------
| Payment Methods Registry
|--------------------------------------------------------------------------
|
| Every payment option shown on Admin > Payment Options is defined here.
| To add a new gateway, add an entry to "methods" (and its logo under
| public/admin/assets/images/payments/gateways). The page, validation and
| storage pick it up automatically; no migration is needed because method
| specific fields are saved in the encrypted "settings" column.
|
| Field keys:
|   name      Input name. instructions, min_order_amount, max_order_amount,
|             display_options, show_icon and sort_order have their own
|             columns; any other name is stored in "settings".
|   label     Field label (also used for the "Please enter ..." message).
|   type      text | password | textarea | amount | number | mode |
|             display_options | show_icon | heading
|             (password values are never shown again; blank keeps the saved one)
|   required  true when the field must be filled to activate.
|   width     full | half (default) | third | quarter | wide
|   help      Optional grey hint after the label.
|   options   For display_options: codes from "display_options" below.
|
*/

$amountFields = [
    ['name' => 'min_order_amount', 'label' => 'Minimum Order Amount', 'type' => 'amount'],
    ['name' => 'max_order_amount', 'label' => 'Maximum Order Amount', 'type' => 'amount'],
];

$instructionsField = [
    'name' => 'instructions',
    'label' => 'Payment instructions',
    'help' => '(This instruction will display to your customers while they are placing order at payment option page)',
    'type' => 'textarea',
    'width' => 'full',
];

$offerIdsField = [
    'name' => 'offer_id',
    'label' => "Offer Id's",
    'help' => '(Please enter comma separated values like offer1,offer2,offer3....)',
    'type' => 'textarea',
    'width' => 'full',
];

$sortOrderField = ['name' => 'sort_order', 'label' => 'Sort Order', 'type' => 'number', 'width' => 'quarter'];

return [

    'groups' => [
        'manual' => 'Manual Payments',
        'gateway' => 'Payments Gateway',
    ],

    'display_options' => [
        'mastercard' => ['label' => 'Mastercard', 'image' => 'admin/assets/images/payments/cards/mastercard.svg'],
        'visa' => ['label' => 'Visa', 'image' => 'admin/assets/images/payments/cards/visa.svg'],
        'amex' => ['label' => 'American Express', 'image' => 'admin/assets/images/payments/cards/amex.svg'],
        'unionpay' => ['label' => 'UnionPay', 'image' => 'admin/assets/images/payments/cards/unionpay.svg'],
        'jcb' => ['label' => 'JCB', 'image' => 'admin/assets/images/payments/cards/jcb.svg'],
        'apple_pay' => ['label' => 'Apple Pay', 'image' => 'admin/assets/images/payments/cards/apple_pay.svg'],
        'google_pay' => ['label' => 'Google Pay', 'image' => 'admin/assets/images/payments/cards/google_pay.svg'],
        'maestro' => ['label' => 'Maestro', 'image' => 'admin/assets/images/payments/cards/maestro.svg'],
        'rupay' => ['label' => 'RuPay', 'image' => 'admin/assets/images/payments/cards/rupay.png'],
        'upi' => ['label' => 'UPI', 'image' => 'admin/assets/images/payments/cards/upi.svg'],
        'net_banking' => ['label' => 'Net Banking', 'image' => 'admin/assets/images/payments/cards/net_banking.svg'],
        'cardemi' => ['label' => 'Card EMI', 'image' => 'admin/assets/images/payments/cards/cardemi.svg'],
    ],

    'methods' => [

        'bank_transfer' => [
            'group' => 'manual',
            'title' => 'Bank Transfer / Deposit',
            'description' => 'Activate bank transfer as payment option for your customers to pay.',
            'logo' => ['type' => 'icon', 'icon' => 'fa-credit-card', 'color' => '#3c763d'],
            'fields' => [
                ...$amountFields,
                [...$instructionsField, 'required' => true],
                $sortOrderField,
            ],
        ],

        'cod' => [
            'group' => 'manual',
            'title' => 'Cash On Delivery (COD)',
            'description' => 'Activate cash on delivery as payment option for your customer to pay.',
            'logo' => ['type' => 'icon', 'icon' => 'fa-money', 'color' => '#666666'],
            'fields' => [
                [...$instructionsField, 'required' => true],
                ['type' => 'heading', 'name' => 'amount_heading', 'label' => 'Set MIN and MAX order amount allowed for COD'],
                ['name' => 'min_order_amount', 'label' => 'MIN Amount', 'type' => 'amount', 'width' => 'third'],
                ['name' => 'max_order_amount', 'label' => 'MAX Amount', 'type' => 'amount', 'width' => 'third'],
                ['name' => 'extra_charge', 'label' => 'COD Charges Retail (If extra)', 'type' => 'amount', 'width' => 'third'],
                $sortOrderField,
            ],
        ],

        'razorpay' => [
            'group' => 'gateway',
            'title' => 'Razorpay Payment Gateway',
            'description' => 'If you have an Razorpay Payment Gateway account, you can activate it for payment from your customers.',
            'logo' => ['type' => 'image', 'path' => 'admin/assets/images/payments/gateways/razorpay.svg'],
            'fields' => [
                ['name' => 'key', 'label' => 'Key', 'type' => 'text', 'required' => true, 'width' => 'quarter'],
                ['name' => 'merchant_name', 'label' => 'Merchant Name', 'type' => 'text', 'required' => true],
                ['name' => 'mode', 'label' => 'Mode', 'type' => 'mode', 'width' => 'quarter'],
                ['name' => 'secret_key', 'label' => 'Secret Key', 'type' => 'password', 'required' => true],
                ['type' => 'heading', 'name' => 'mode_spacer', 'label' => ''],
                ...$amountFields,
                ['type' => 'display_options', 'name' => 'display_options', 'options' => ['mastercard', 'visa', 'maestro', 'net_banking', 'amex', 'cardemi', 'upi']],
                $instructionsField,
                $offerIdsField,
                ['type' => 'show_icon', 'name' => 'show_icon', 'label' => 'Show Razorpay icon'],
                $sortOrderField,
            ],
        ],

        'phonepe' => [
            'group' => 'gateway',
            'title' => 'Phonepe',
            'description' => 'If you have an Phonepe Payment Gateway account, you can activate it for payment from your customers.',
            'logo' => ['type' => 'image', 'path' => 'admin/assets/images/payments/gateways/phonepe.svg'],
            'fields' => [
                ['name' => 'merchant_id', 'label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                ['name' => 'mode', 'label' => 'Mode', 'type' => 'mode'],
                ['name' => 'key', 'label' => 'Key', 'type' => 'text', 'required' => true],
                ['name' => 'key_index', 'label' => 'Key Index', 'type' => 'text', 'required' => true],
                ['name' => 'android_app_id', 'label' => 'Android App ID', 'type' => 'text', 'required' => true],
                ['name' => 'ios_app_id', 'label' => 'iOS App ID', 'type' => 'text', 'required' => true],
                ...$amountFields,
                $instructionsField,
                ['type' => 'show_icon', 'name' => 'show_icon', 'label' => 'Show PhonePe icon'],
                $sortOrderField,
            ],
        ],

        'stripe_hosted' => [
            'group' => 'gateway',
            'title' => 'Stripe Hosted',
            'description' => 'If you have a stripe Payment Gateway account, you can activate it for payment from your customers.',
            'logo' => ['type' => 'image', 'path' => 'admin/assets/images/payments/gateways/stripe.svg'],
            'fields' => [
                ['name' => 'publishable_key', 'label' => 'Publishable Key', 'type' => 'text', 'required' => true],
                ['name' => 'secret_key', 'label' => 'Secret Key', 'type' => 'password', 'required' => true],
                ['name' => 'mode', 'label' => 'Mode', 'type' => 'mode'],
                ['type' => 'heading', 'name' => 'mode_spacer', 'label' => ''],
                ...$amountFields,
                ['type' => 'display_options', 'name' => 'display_options', 'options' => ['mastercard', 'visa', 'amex', 'unionpay', 'jcb', 'apple_pay', 'google_pay']],
                $instructionsField,
                ['type' => 'show_icon', 'name' => 'show_icon', 'label' => 'Show stripe_hosted icon'],
                $sortOrderField,
            ],
        ],

        'instamojo' => [
            'group' => 'gateway',
            'title' => 'Instamojo Payment Gateway',
            'description' => 'If you have an Instamojo Payment Gateway account, you can activate it for payment from your customers.',
            'logo' => ['type' => 'image', 'path' => 'admin/assets/images/payments/gateways/instamojo.png'],
            'fields' => [
                ['name' => 'api_key', 'label' => 'API Key', 'type' => 'text', 'required' => true],
                ['name' => 'auth_token', 'label' => 'Auth Token', 'type' => 'password', 'required' => true],
                ['name' => 'salt', 'label' => 'Salt', 'type' => 'password', 'required' => true, 'width' => 'wide'],
                ['name' => 'mode', 'label' => 'Mode', 'type' => 'mode', 'width' => 'third'],
                ...$amountFields,
                ['type' => 'display_options', 'name' => 'display_options', 'options' => ['mastercard', 'visa', 'maestro', 'net_banking']],
                $instructionsField,
                ['type' => 'show_icon', 'name' => 'show_icon', 'label' => 'Show Instamojo icon'],
                $sortOrderField,
            ],
        ],

    ],

];
