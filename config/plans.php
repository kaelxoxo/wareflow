<?php
define('PLANS', [
    'starter' => [
        'name'            => 'Starter',
        'cents'           => 0,
        'inventory_limit' => 50,
        'member_limit'    => 3,
        'features'        => [
            ['inventory_2', 'Up to 50 inventory items'],
            ['group',       'Up to 3 team members'],
            ['warehouse',   'Multi-warehouse support'],
            ['bar_chart',   'KPI dashboard and charts'],
            ['tune',        'Custom fields'],
            ['swap_horiz',  'Stock movement tracking'],
        ],
    ],
    'pro' => [
        'name'            => 'Pro',
        'cents'           => 500,
        'inventory_limit' => 500,
        'member_limit'    => 5,
        'stripe_label'    => 'Wareflow Pro — Monthly',
        'features'        => [
            ['inventory_2', 'Up to 500 inventory items & SKUs'],
            ['warehouse',   'Multi-warehouse support'],
            ['swap_horiz',  'Full stock movement history'],
            ['group',       'Up to 5 team members with roles'],
            ['tune',        'Custom fields on any entity'],
            ['bar_chart',   'KPI dashboard and trend charts'],
        ],
    ],
    'max' => [
        'name'            => 'Max',
        'cents'           => 1000,
        'inventory_limit' => null,
        'member_limit'    => 20,
        'stripe_label'    => 'Wareflow Max — Monthly',
        'features'        => [
            ['all_inclusive', 'Unlimited inventory items & SKUs'],
            ['warehouse',     'Unlimited warehouses'],
            ['swap_horiz',    'Full stock movement history'],
            ['group',         'Up to 20 team members with roles'],
            ['tune',          'Custom fields on any entity'],
            ['bar_chart',     'Advanced analytics & KPI trends'],
        ],
    ],
]);
