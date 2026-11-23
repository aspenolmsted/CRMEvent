<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$popupMeta = [
    'moduleMain' => 'ev_TicketTypes',
    'varName' => 'EV_TICKETTYPES',
    'orderBy' => 'ev_ticket_types.name',
    'whereClauses' => [
        'name' => 'ev_ticket_types.name',
    ],
    'searchInputs' => [
        'name',
    ],
    'searchdefs' => [
        'name' => [
            'name' => 'name',
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
        'PRICE' => [
            'width' => '20%',
            'label' => 'LBL_PRICE',
            'default' => true,
        ],
        'STATUS' => [
            'width' => '15%',
            'label' => 'LBL_STATUS',
            'default' => true,
        ],
    ],
];
