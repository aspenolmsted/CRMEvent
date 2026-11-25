<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$popupMeta = [
    'moduleMain' => 'ev_Venues',
    'varName' => 'EV_VENUES',
    'orderBy' => 'ev_venues.name',
    'whereClauses' => [
        'name' => 'ev_venues.name',
        'address_city' => 'ev_venues.address_city',
    ],
    'searchInputs' => [
        'name',
        'address_city',
    ],
    'searchdefs' => [
        'name' => [
            'name' => 'name',
            'width' => '10%',
        ],
        'address_city' => [
            'name' => 'address_city',
            'width' => '10%',
        ],
    ],
    'listviewdefs' => [
        'NAME' => [
            'width' => '40%',
            'label' => 'LBL_NAME',
            'link' => true,
            'default' => true,
        ],
        'ADDRESS_CITY' => [
            'width' => '30%',
            'label' => 'LBL_ADDRESS_CITY',
            'default' => true,
        ],
        'TOTAL_CAPACITY' => [
            'width' => '30%',
            'label' => 'LBL_TOTAL_CAPACITY',
            'default' => true,
        ],
    ],
];
