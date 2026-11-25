<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$dictionary['ev_VenueSeats'] = [
    'table' => 'ev_venue_seats',
    'audited' => false,
    'fields' => [
        'venue_id' => [
            'name' => 'venue_id',
            'vname' => 'LBL_VENUE_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'venue_name' => [
            'name' => 'venue_name',
            'rname' => 'name',
            'id_name' => 'venue_id',
            'vname' => 'LBL_VENUE_NAME',
            'type' => 'relate',
            'link' => 'ev_venues',
            'table' => 'ev_venues',
            'isnull' => 'true',
            'module' => 'ev_Venues',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'required' => true,
        ],
        'ev_venues' => [
            'name' => 'ev_venues',
            'type' => 'link',
            'relationship' => 'ev_venues_ev_venue_seats',
            'source' => 'non-db',
            'vname' => 'LBL_VENUE',
        ],
        'section_name' => [
            'name' => 'section_name',
            'vname' => 'LBL_SECTION_NAME',
            'type' => 'varchar',
            'len' => '100',
        ],
        'row_label' => [
            'name' => 'row_label',
            'vname' => 'LBL_ROW_LABEL',
            'type' => 'varchar',
            'len' => '50',
        ],
        'seat_number' => [
            'name' => 'seat_number',
            'vname' => 'LBL_SEAT_NUMBER',
            'type' => 'varchar',
            'len' => '50',
        ],
        'seat_type' => [
            'name' => 'seat_type',
            'vname' => 'LBL_SEAT_TYPE',
            'type' => 'enum',
            'options' => 'ev_seat_type_dom',
            'len' => '50',
            'default' => 'Standard',
        ],
        'pos_x' => [
            'name' => 'pos_x',
            'vname' => 'LBL_POS_X',
            'type' => 'int',
            'len' => '11',
            'default' => 0,
        ],
        'pos_y' => [
            'name' => 'pos_y',
            'vname' => 'LBL_POS_Y',
            'type' => 'int',
            'len' => '11',
            'default' => 0,
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
    ],
    'indices' => [
        [
            'name' => 'idx_ev_venue_seats_venue',
            'type' => 'index',
            'fields' => ['venue_id', 'deleted'],
        ],
    ],
];

VardefManager::createVardef('ev_VenueSeats', 'ev_VenueSeats', ['basic', 'assignable']);
