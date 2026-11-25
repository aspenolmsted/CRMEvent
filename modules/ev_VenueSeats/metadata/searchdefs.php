<?php
// modules/ev_VenueSeats/metadata/searchdefs.php
$searchdefs['ev_VenueSeats'] = [
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
            'section_name',
            'seat_type',
        ],
        'advanced_search' => [
            'name',
            'venue_name',
            'section_name',
            'seat_type',
            'assigned_user_id',
        ],
    ],
];