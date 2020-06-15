<?php

add_filter( 'cp_element_builder', function ( $elements ) {
    $elements[] = [
        "label"    => "Text Field",
        "id"       => "text_field",
        "icon"     => "form",
        "category" => "Basic",
        "name"     => "",
        "user_input" => "",
        "field_name" => "",
        "settings" => [

            "label" => [
                "type"  => "input",
                "value" => "",
                "label" => "Label"
            ],

            "placeholder" => [
                "type"  => "input",
                "value" => "",
                "label" => "Placeholder"
            ],

            "default" => [
                "type"  => "input",
                "value" => "",
                "label" => "Default value"
            ],

            "required" => [
                "type"  => "switch",
                "value" => false,
                "label" => "Required"
            ],

            "type" => [
                "type"    => "radio_button",
                "value"   => "text",
                "label"   => "Type",
                "validation"  => [
                    "limit"   => 0,
                    "whitelist" => []
                ],
                "options" => [
                    "text"     => "Text field",
                    "email"    => "Email",
                    "number"   => "Number",
                    "password" => "Password"
                ]
            ]
        ],
        "condition_settings" => [
            "or" => [
                ["and" =>
                    [
                        [
                            "target" => '',
                            "condition" => '!=empty',
                            "value" => ''
                        ]
                    ]
                ]
            ]
        ]
    ];
    return $elements;
} );


add_filter( 'cp_element_builder', function ( $elements ) {
    $elements[] = [
        "label"    => "Radio",
        "id"       => "radio",
        "category" => "Basic",
        "icon"     => "check-circle",
        "name"     => "",
        "user_input" => "",
        "field_name" => "",
        "settings" => [
            "label" => [
                "type"  => "input",
                "value" => "",
                "label" => "Label"
            ],

            "default" => [
                "type"  => "input",
                "value" => "",
                "label" => "Default value"
            ],

            "options" => [
                "label"       => "Options",
                "type"        => "textarea",
                "value"       => "",
                "placeholder" => "value\r\nvalue",
                "pricing"     => []
            ]
        ],
        "condition_settings" => [
            "or" => [
                ["and" =>
                    [
                        [
                            "target" => '',
                            "condition" => '!=empty',
                            "value" => ''
                        ]
                    ]
                ]
            ]
        ]
    ];
    return $elements;
} );

add_filter( 'cp_element_builder', function ( $elements ) {
    $elements[] = [
        "label"    => "Checkbox",
        "id"       => "checkbox",
        "category" => "Basic",
        "icon"     => "check-square",
        "name"     => "",
        "user_input" => "",
        "field_name" => "",
        "settings" => [
            "label" => [
                "type"  => "input",
                "value" => "",
                "label" => "Label"
            ],

            "default" => [
                "type"  => "input",
                "value" => "",
                "label" => "Default value"
            ],

            "options" => [
                "label"       => "Options",
                "type"        => "textarea",
                "value"       => "",
                "placeholder" => "value\r\nvalue",
                "pricing"     => []
            ]
        ],
        "condition_settings" => [
            "or" => [
                ["and" =>
                    [
                        [
                            "target" => '',
                            "condition" => '!=empty',
                            "value" => ''
                        ]
                    ]
                ]
            ]
        ]
    ];
    return $elements;
} );

add_filter( 'cp_element_builder', function ( $elements ) {
    $elements[] = [
        "label"    => "Select Box",
        "id"       => "select",
        "category" => "Basic",
        "icon"     => "menu-unfold",
        "name"     => "",
        "user_input" => "",
        "field_name" => "",
        "settings" => [

            "label" => [
                "type"  => "input",
                "value" => "",
                "label" => "Label"
            ],

            "default" => [
                "type"  => "input",
                "value" => "",
                "label" => "Default value"
            ],

            "options" => [
                "label"       => "Options",
                "type"        => "textarea",
                "value"       => "",
                "placeholder" => "value\r\nvalue",
                "pricing"     => []
            ]
        ],
        "condition_settings" => [
            "or" => [
                ["and" =>
                    [
                        [
                            "target" => '',
                            "condition" => '!=empty',
                            "value" => ''
                        ]
                    ]
                ]
            ]
        ]
    ];
    return $elements;
} );

add_filter( 'cp_element_builder', function ( $elements ) {
    $elements[] = [
        "label"    => "Date Time Picker",
        "id"       => "date-time-picker",
        "category" => "Basic",
        "icon"     => "calendar",
        "name"     => "",
        "user_input" => "",
        "field_name" => "",
        "settings" => [
            "label" => [
                "type"  => "input",
                "value" => "",
                "label" => "Label"
            ],
            "default" => [
                "type"  => "picker",
                "value" => "",
                "label" => "Default value",
                "format" => "YYYY-MM-DDTHH:mm"
            ]
        ],
        "condition_settings" => [
            "or" => [
                ["and" =>
                    [
                        [
                            "target" => '',
                            "condition" => '!=empty',
                            "value" => ''
                        ]
                    ]
                ]
            ]
        ]
    ];
    return $elements;
} );

add_filter( 'cp_element_builder', function ( $elements ) {
    $elements[] = [
        "label"    => "Slide Range",
        "id"       => "slide-range",
        "category" => "Basic",
        "icon"     => "colum-height",
        "name"     => "",
        "user_input" => "",
        "field_name" => "",
        "settings" => [
            "label" => [
                "type"  => "input",
                "value" => "",
                "label" => "Label"
            ],
            "default" => [
                "type"  => "input",
                "value" => "0",
                "label" => "Default value"
            ],
            "max_value" => [
                "type"  => "input",
                "value" => "100",
                "label" => "Max value"
            ],
            "min_value" => [
                "type"  => "input",
                "value" => "0",
                "label" => "Min value"
            ],
            "step" => [
                "type"  => "input",
                "value" => "1",
                "label" => "Step"
            ],
            "prepend" => [
                "type"  => "input",
                "value" => "",
                "label" => "Prepend",
                "placeholder" => "Appears before the input"
            ],
            "append" => [
                "type"  => "input",
                "value" => "",
                "label" => "Append",
                "placeholder" => "Appears after the input"
            ]
        ],
        "condition_settings" => [
            "or" => [
                ["and" =>
                    [
                        [
                            "target" => '',
                            "condition" => '!=empty',
                            "value" => ''
                        ]
                    ]
                ]
            ]
        ]
    ];
    return $elements;
} );

add_filter( 'cp_element_builder', function ( $elements ) {
    $elements[] = [
        "label"    => "File upload",
        "id"       => "file-upload",
        "category" => "Basic",
        "icon"     => "cloud-upload",
        "name"     => "",
        "user_input" => "",
        "field_name" => "",
        "settings" => [
            "label" => [
                "type"  => "input",
                "value" => "",
                "label" => "Label"
            ],
            /*"min_file_size" => [
                "type"  => "input",
                "disabled" => true,
                "value" => "0",
                "label" => "Minimum size (MB)",
                "placeholder" => "Restrict which files can be uploaded"
            ],
            "max_file_size" => [
                "type"  => "input",
                "disabled" => true,
                "value" => "2",
                "label" => "Maximum size (MB)",
                "placeholder" => "Restrict which files can be uploaded"
            ],
            "options" => [
                "type"  => "textarea",
                "value" => "",
                "label" => "File types",
                "placeholder" => "Comma separated list. Leave blank for all types"
            ],*/
        ],
        "condition_settings" => [
            "or" => [
                ["and" =>
                    [
                        [
                            "target" => '',
                            "condition" => '!=empty',
                            "value" => ''
                        ]
                    ]
                ]
            ]
        ]
    ];
    return $elements;
} );

add_filter( 'cp_element_builder', function ( $elements ) {
    $elements[] = [
        "label"    => "Image upload",
        "id"       => "image-upload",
        "category" => "Basic",
        "icon"     => "picture",
        "name"     => "",
        "user_input" => "",
        "field_name" => "",
        "settings" => [
            "label" => [
                "type"  => "input",
                "value" => "",
                "label" => "Label"
            ],
            /*"return_file_value"  => [
                "type"            => "radio_button",
                "value"           => "",
                "label"           => "Return value",
                "options" => [
                    "fileArray"   => "File array",
                    "fileURL"     => "File URL",
                    "fileID"      => "File ID"
                ]
            ],
            "required" => [
                "type"  => "switch",
                "value" => false,
                "label" => "Required"
            ],
            "multiple_inputs_minimum" => [
                "type"  => "multiple_input",
                "label" => "Minimum",
                "number" => 0,
                "values" => [
                    [
                        "prefix" => "Width",
                        "value"  => "",
                        "suffix" => "px"
                    ],
                    [
                        "prefix" => "Height",
                        "value"  => "",
                        "suffix" => "px"
                    ],
                    [
                        "prefix" => "File size",
                        "value"  => "",
                        "suffix" => "MB"
                    ]
                ]
            ],
            "multiple_inputs_maximumm" => [
                "type"  => "multiple_input",
                "label" => "Maximum",
                "number" => 0,
                "values" => [
                    [
                        "prefix" => "Width",
                        "value"  => "",
                        "suffix" => "px"
                    ],
                    [
                        "prefix" => "Height",
                        "value"  => "",
                        "suffix" => "px"
                    ],
                    [
                        "prefix" => "File size",
                        "value"  => "",
                        "suffix" => "MB"
                    ]
                ]
            ]*/
        ],
        "condition_settings" => [
            "or" => [
                ["and" =>
                    [
                        [
                            "target" => '',
                            "condition" => '!=empty',
                            "value" => ''
                        ]
                    ]
                ]
            ]
        ]
    ];
    return $elements;
} );

add_filter( 'cp_element_builder', function ( $elements ) {
    $elements[] = [
        "label"    => "Text Area",
        "id"       => "text-area",
        "category" => "Basic",
        "icon"     => "align-center",
        "name"     => "",
        "user_input" => "",
        "field_name" => "",
        "settings" => [
            "label" => [
                "type"  => "input",
                "value" => "",
                "label" => "Label"
            ],
            "default" => [
                "type"  => "textarea",
                "value" => "",
                "label" => "Default value"
            ],
            "placeholder" => [
                "type"  => "input",
                "value" => "",
                "label" => "Placeholder text"
            ],
            "limit_character" => [
                "type"  => "input",
                "value" => "",
                "label" => "Character limit",
                "constraint" => "number"
            ],
            "required" => [
                "type"  => "switch",
                "value" => false,
                "label" => "Required"
            ]
        ],
        "condition_settings" => [
            "or" => [
                ["and" =>
                    [
                        [
                            "target" => '',
                            "condition" => '!=empty',
                            "value" => ''
                        ]
                    ]
                ]
            ]
        ]
    ];
    return $elements;
} );

add_filter( 'cp_element_builder', function ( $elements ) {
    $elements[] = [
        "label"    => "Repeater",
        "id"       => "repeater",
        "category" => "Basic",
        "icon"     => "redo",
        "name"     => "",
        "user_input" => "",
        "field_name" => "",
        "is_hide" => false,
        "settings" => [
            "label" => [
                "type"  => "input",
                "value" => "",
                "label" => "Label"
            ],
            "cols_numb" => [
                "type"  => "input",
                "value" => "1",
                "label" => "Number of column",
                "constraint" => "number"
            ],
            "rows_numb" => [
                "type"  => "input",
                "value" => "1",
                "label" => "Number of row",
                "constraint" => "number"
            ],
            "layout"    => [
                "rows"  => [
                    [
                        "cols" => [
                            [
                                "type" => "col",
                                "width" => [
                                    "md" => 12,
                                    "sm" => 12,
                                    "xs" => 12
                                ],
                                "elements" => []
                            ]
                        ]
                    ]
                ]
            ]
        ],
        "condition_settings" => [
            "or" => [
                ["and" =>
                    [
                        [
                            "target" => '',
                            "condition" => '!=empty',
                            "value" => ''
                        ]
                    ]
                ]
            ]
        ]
    ];
    return $elements;
} );


add_filter( 'cp_location_builder', function ( $locations ) {
    $locations = [
        'single_product' => [
            'location' => [
                'enabled' => false,
                'whereToShow' => 'woocommerce_before_add_to_cart_button',
                'whenToShow' => [
                    'or' => [
                        [
                            'and' => [
                                [
                                    'target' => 'User',
                                    'value' => [],
                                    'id' => [],
                                    'label' =>[]
                                ]
                            ]
                        ]
                    ]
                ]

            ]
        ],
        'checkout' => [
            'location' => [
                'enabled' => false,
                'whereToShow' => 'woocommerce_before_checkout_billing_form',
                'whenToShow' => [
                    'or' => [
                        [
                            'and' => [
                                [
                                    'target' => 'User',
                                    'value' => [],
                                    'id' => [],
                                    'label' =>[]
                                ]
                            ]
                        ]
                    ]
                ]

            ]
        ],
        'my_account' => [
            'location' => [
                'enabled' => false,
                'whereToShow' => 'woocommerce_edit_account_form_start',
                'whenToShow' => [
                    'or' => [
                        [
                            'and' => [
                                [
                                    'target' => 'User',
                                    'value' => [],
                                    'id' => [],
                                    'label' =>[]
                                ]
                            ]
                        ]
                    ]
                ]

            ]
        ]
    ];
    return $locations;
} );

add_filter( 'cp_hooks_data', function ( $hook ) {
    $hooks = [
        'single_product' => [
            [
                'label' => 'Before Add To Cart Button',
                'value' => 'woocommerce_before_add_to_cart_button'
            ],
            [
                'label' => 'Before Single Variation',
                'value' => 'woocommerce_before_single_variation'
            ],
            [
                'label' => 'After Single Variation',
                'value' => 'woocommerce_after_single_variation'
            ],
            [
                'label' => 'After Add To Cart Button',
                'value' => 'woocommerce_after_add_to_cart_button'
            ]

        ],
        'checkout' => [
            [
                'label' => 'Before Billing Form',
                'value' => 'woocommerce_before_checkout_billing_form'
            ],
            [
                'label' => 'After Billing Form',
                'value' => 'woocommerce_after_checkout_billing_form'
            ],
            [
                'label' => 'Before Shipping Form',
                'value' => 'woocommerce_before_checkout_shipping_form'
            ],
            [
                'label' => 'After Shipping Form',
                'value' => 'woocommerce_after_checkout_shipping_form'
            ],
            [
                'label' => 'Before Order Notes',
                'value' => 'woocommerce_before_order_notes'
            ],
            [
                'label' => 'After Order Notes',
                'value' => 'woocommerce_after_order_notes'
            ]
        ],
        'my_account' => [
            [
				'label' => 'Account Details - Form Start',
				'value' => 'woocommerce_edit_account_form_start'
            ],
            [
                'label' => 'Account Details - Form End',
                'value' => 'woocommerce_edit_account_form_end'
            ],
            [
                'label' => 'Billing Address - Form Start',
                'value' => 'woocommerce_before_edit_address_form_billing'
            ],
            [
                'label' => 'Billing Address - Form End',
                'value' => 'woocommerce_after_edit_address_form_billing'
            ],
            [
                'label' => 'Shipping Address - Form Start',
                'value' => 'woocommerce_before_edit_address_form_shipping'
            ],
            [
                'label' => 'Shipping Address - Form End',
                'value' => 'woocommerce_after_edit_address_form_shipping'
            ]
        ]
    ];
    return $hooks;
} );