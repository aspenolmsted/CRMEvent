<?php
$viewdefs['ev_Venues']['EditView'] = [
    'templateMeta' => [
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [
        'default' => [
            ['name', 'total_capacity'],
            ['address_street', 'address_city'],
            ['address_state', 'address_postalcode'],
            ['assigned_user_name'],
            ['description'],
        ],
    ],
];
