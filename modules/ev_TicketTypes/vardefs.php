<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$dictionary['ev_TicketTypes'] = [
    'table' => 'ev_ticket_types',
    'audited' => true,
    'fields' => [
        'event_id' => [
            'name' => 'event_id',
            'vname' => 'LBL_EVENT_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => false,
        ],
        'event_name' => [
            'name' => 'event_name',
            'rname' => 'name',
            'id_name' => 'event_id',
            'vname' => 'LBL_EVENT_NAME',
            'type' => 'relate',
            'link' => 'fp_events',
            'table' => 'fp_events',
            'isnull' => 'true',
            'module' => 'FP_events',
            'dbType' => 'varchar',
            'len' => '255',
            'source' => 'non-db',
            'required' => true,
        ],
        'fp_events' => [
            'name' => 'fp_events',
            'type' => 'link',
            'relationship' => 'fp_events_ev_ticket_types',
            'source' => 'non-db',
            'vname' => 'LBL_EVENT',
        ],
        'price' => [
            'name' => 'price',
            'vname' => 'LBL_PRICE',
            'type' => 'currency',
            'dbType' => 'decimal',
            'len' => '26,6',
            'default' => '0.000000',
            'required' => true,
        ],
        'fair_market_value' => [
            'name' => 'fair_market_value',
            'vname' => 'LBL_FAIR_MARKET_VALUE',
            'type' => 'currency',
            'dbType' => 'decimal',
            'len' => '26,6',
            'default' => '0.000000',
        ],
        'currency_id' => [
            'name' => 'currency_id',
            'type' => 'id',
            'vname' => 'LBL_CURRENCY',
            'default' => '-99',
        ],
        'quantity_available' => [
            'name' => 'quantity_available',
            'vname' => 'LBL_QUANTITY_AVAILABLE',
            'type' => 'int',
            'len' => '11',
            'default' => 0,
        ],
        'quantity_sold' => [
            'name' => 'quantity_sold',
            'vname' => 'LBL_QUANTITY_SOLD',
            'type' => 'int',
            'len' => '11',
            'default' => 0,
            'readonly' => true,
        ],
        'sales_start_date' => [
            'name' => 'sales_start_date',
            'vname' => 'LBL_SALES_START_DATE',
            'type' => 'datetime',
        ],
        'sales_end_date' => [
            'name' => 'sales_end_date',
            'vname' => 'LBL_SALES_END_DATE',
            'type' => 'datetime',
        ],
        'status' => [
            'name' => 'status',
            'vname' => 'LBL_STATUS',
            'type' => 'enum',
            'options' => 'ev_ticket_status_dom',
            'len' => '50',
            'default' => 'Active',
        ],
        'ev_registrations' => [
            'name' => 'ev_registrations',
            'type' => 'link',
            'relationship' => 'ev_ticket_types_ev_registrations',
            'source' => 'non-db',
            'vname' => 'LBL_REGISTRATIONS_SUBPANEL_TITLE',
        ],
    ],
    'relationships' => [
        'fp_events_ev_ticket_types' => [
            'lhs_module' => 'FP_events',
            'lhs_table' => 'fp_events',
            'lhs_key' => 'id',
            'rhs_module' => 'ev_TicketTypes',
            'rhs_table' => 'ev_ticket_types',
            'rhs_key' => 'event_id',
            'relationship_type' => 'one-to-many',
        ],
        'ev_ticket_types_ev_registrations' => [
            'lhs_module' => 'ev_TicketTypes',
            'lhs_table' => 'ev_ticket_types',
            'lhs_key' => 'id',
            'rhs_module' => 'ev_Registrations',
            'rhs_table' => 'ev_registrations',
            'rhs_key' => 'ticket_type_id',
            'relationship_type' => 'one-to-many',
        ],
    ],
    'indices' => [
        [
            'name' => 'idx_ev_ticket_event_id',
            'type' => 'index',
            'fields' => ['event_id', 'deleted'],
        ],
    ],
];

VardefManager::createVardef('ev_TicketTypes', 'ev_TicketTypes', ['basic', 'assignable']);
