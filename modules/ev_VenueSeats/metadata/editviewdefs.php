<?php
$viewdefs['ev_VenueSeats']['EditView'] = [
    'templateMeta' => [
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [
        'default' => [
            ['name', 'venue_name'],
            ['section_name', 'seat_type'],
            ['row_label', 'seat_number'],
            ['pos_x', 'pos_y'],
        ],
    ],
];
