<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$popupMeta = [
    'moduleMain' => 'ev_EventSeats',
    'varName' => 'EV_EVENTSEATS',
    'orderBy' => 'ev_event_seats.name',
    'whereClauses' => [
        'name' => 'ev_event_seats.name',
        'section_name' => 'ev_event_seats.section_name',
    ],
    'searchInputs' => [
        'name',
        'section_name',
    ],
    'searchdefs' => [
        'name' => [
            'name' => 'name',
            'width' => '10%',
        ],
        'section_name' => [
            'name' => 'section_name',
            'width' => '10%',
        ],
    ],
    'listviewdefs' => [
        'NAME' => [
            'width' => '35%',
            'label' => 'LBL_NAME',
            'link' => true,
            'default' => true,
        ],
        'EVENT_NAME' => [
            'width' => '30%',
            'label' => 'LBL_EVENT_NAME',
            'default' => true,
        ],
        'SECTION_NAME' => [
            'width' => '20%',
            'label' => 'LBL_SECTION_NAME',
            'default' => true,
        ],
        'SEAT_STATUS' => [
            'width' => '15%',
            'label' => 'LBL_SEAT_STATUS',
            'default' => true,
        ],
    ],
];
