<?php
$viewdefs['ev_TicketTypes']['DetailView'] = [
    'templateMeta' => [
        'form' => ['buttons' => ['EDIT', 'DUPLICATE', 'DELETE']],
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
            ['quantity_available', 'quantity_sold'],
            ['sales_start_date', 'sales_end_date'],
            ['status', 'assigned_user_name'],
            ['description'],
        ],
    ],
];
