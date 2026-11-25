<?php
$searchdefs['ev_EventSeats'] = [
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
            'seat_status',
        ],
        'advanced_search' => [
            'name',
            'event_name',
            'section_name',
            'ticket_type_name',
            'seat_status',
            'assigned_user_id',
        ],
    ],
];
