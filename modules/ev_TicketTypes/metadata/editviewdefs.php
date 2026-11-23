<?php
$viewdefs['ev_TicketTypes']['EditView'] = [
    'templateMeta' => [
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [
        'default' => [
            ['name', 'event_name'],
            ['price', 'fair_market_value'],
            ['quantity_available', 'status'],
            ['sales_start_date', 'sales_end_date'],
            ['assigned_user_name', 'currency_id'],
            ['description'],
        ],
    ],
];
