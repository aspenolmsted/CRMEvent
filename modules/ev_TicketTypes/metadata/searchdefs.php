<?php
$searchdefs['ev_TicketTypes'] = [
    'templateMeta' => [
        'maxColumns' => '3',
        'maxColumnsBasic' => '3',
        'widths' => [
            'label' => '10',
            'field' => '30',
        ],
    ],
    'layout' => [
        'basic_search' => [
            'name',
            'event_name',
            'status',
        ],
        'advanced_search' => [
            'name',
            'event_name',
            'price',
            'status',
            'assigned_user_id',
        ],
    ],
];
