<?php
$viewdefs['ev_EventSeats']['EditView'] = [
    'templateMeta' => [
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [
        'default' => [
            ['name', 'seat_status'],
            ['event_name', 'section_name'],
            ['ticket_type_name', 'registration_name'],
            ['hold_expires_at', 'assigned_user_name'],
            ['description'],
        ],
    ],
];
