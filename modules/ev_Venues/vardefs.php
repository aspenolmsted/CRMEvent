<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$dictionary['ev_Venues'] = [
    'table' => 'ev_venues',
    'audited' => true,
    'fields' => [
        'address_street' => [
            'name' => 'address_street',
            'vname' => 'LBL_ADDRESS_STREET',
            'type' => 'varchar',
            'len' => '150',
        ],
        'address_city' => [
            'name' => 'address_city',
            'vname' => 'LBL_ADDRESS_CITY',
            'type' => 'varchar',
            'len' => '100',
        ],
        'address_state' => [
            'name' => 'address_state',
            'vname' => 'LBL_ADDRESS_STATE',
            'type' => 'varchar',
            'len' => '100',
        ],
        'address_postalcode' => [
            'name' => 'address_postalcode',
            'vname' => 'LBL_ADDRESS_POSTALCODE',
            'type' => 'varchar',
            'len' => '20',
        ],
        'total_capacity' => [
            'name' => 'total_capacity',
            'vname' => 'LBL_TOTAL_CAPACITY',
            'type' => 'int',
            'len' => '11',
            'default' => 0,
        ],
        'layout_json' => [
            'name' => 'layout_json',
            'vname' => 'LBL_LAYOUT_JSON',
            'type' => 'text',
        ],
        'ev_venue_seats' => [
            'name' => 'ev_venue_seats',
            'type' => 'link',
            'relationship' => 'ev_venues_ev_venue_seats',
            'source' => 'non-db',
            'vname' => 'LBL_VENUE_SEATS_SUBPANEL_TITLE',
        ],
        'fp_events' => [
            'name' => 'fp_events',
            'type' => 'link',
            'relationship' => 'ev_venues_fp_events',
            'source' => 'non-db',
            'vname' => 'LBL_EVENTS_SUBPANEL_TITLE',
        ],
    ],
    'relationships' => [
        'ev_venues_ev_venue_seats' => [
            'lhs_module' => 'ev_Venues',
            'lhs_table' => 'ev_venues',
            'lhs_key' => 'id',
            'rhs_module' => 'ev_VenueSeats',
            'rhs_table' => 'ev_venue_seats',
            'rhs_key' => 'venue_id',
            'relationship_type' => 'one-to-many',
        ],
        'ev_venues_fp_events' => [
            'lhs_module' => 'ev_Venues',
            'lhs_table' => 'ev_venues',
            'lhs_key' => 'id',
            'rhs_module' => 'FP_events',
            'rhs_table' => 'fp_events',
            'rhs_key' => 'venue_id_c',
            'relationship_type' => 'one-to-many',
        ],
    ],
];

VardefManager::createVardef('ev_Venues', 'ev_Venues', ['basic', 'assignable']);
