<?php
$listViewDefs['ev_EventSeats'] = [
    'NAME' => [
        'width' => '25%',
        'label' => 'LBL_NAME',
        'default' => true,
        'link' => true,
    ],
    'EVENT_NAME' => [
        'width' => '25%',
        'label' => 'LBL_EVENT_NAME',
        'id' => 'EVENT_ID',
        'module' => 'FP_events',
        'default' => true,
        'link' => true,
    ],
    'SECTION_NAME' => [
        'width' => '15%',
        'label' => 'LBL_SECTION_NAME',
        'default' => true,
    ],
    'TICKET_TYPE_NAME' => [
        'width' => '15%',
        'label' => 'LBL_TICKET_TYPE_NAME',
        'id' => 'TICKET_TYPE_ID',
        'module' => 'ev_TicketTypes',
        'default' => true,
        'link' => true,
    ],
    'SEAT_STATUS' => [
        'width' => '20%',
        'label' => 'LBL_SEAT_STATUS',
        'default' => true,
    ],
];
