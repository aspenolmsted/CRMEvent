<?php
$listViewDefs['ev_TicketTypes'] = [
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
    'PRICE' => [
        'width' => '15%',
        'label' => 'LBL_PRICE',
        'default' => true,
    ],
    'QUANTITY_AVAILABLE' => [
        'width' => '10%',
        'label' => 'LBL_QUANTITY_AVAILABLE',
        'default' => true,
    ],
    'QUANTITY_SOLD' => [
        'width' => '10%',
        'label' => 'LBL_QUANTITY_SOLD',
        'default' => true,
    ],
    'STATUS' => [
        'width' => '15%',
        'label' => 'LBL_STATUS',
        'default' => true,
    ],
];
