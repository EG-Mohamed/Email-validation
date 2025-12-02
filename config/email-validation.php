<?php

return [
    'validations' => [
        'syntax' => true,
        'dns' => true,
        'disposable' => true,
    ],

    'messages' => [
        'syntax' => __('The :attribute must be a valid email address.'),
        'dns' => __('The :attribute domain does not have valid MX records.'),
        'disposable' => __('Disposable email addresses are not allowed.'),
    ],
];
