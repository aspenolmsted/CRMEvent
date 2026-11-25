<?php

$dictionary['FP_events']['fields']['venue_id_c'] = [
    'name' => 'venue_id_c',
    'vname' => 'LBL_VENUE_ID',
    'type' => 'id',
    'reportable' => false,
    'studio' => false,
];

$dictionary['FP_events']['fields']['venue_name_c'] = [
    'name' => 'venue_name_c',
    'rname' => 'name',
    'id_name' => 'venue_id_c',
    'vname' => 'LBL_VENUE_NAME',
    'type' => 'relate',
    'link' => 'ev_venues',
    'table' => 'ev_venues',
    'isnull' => 'true',
    'module' => 'ev_Venues',
    'dbType' => 'varchar',
    'len' => '255',
    'source' => 'non-db',
    'studio' => 'visible',
];

$dictionary['FP_events']['fields']['ev_venues'] = [
    'name' => 'ev_venues',
    'type' => 'link',
    'relationship' => 'ev_venues_fp_events',
    'source' => 'non-db',
    'vname' => 'LBL_VENUE_NAME',
];

$dictionary['FP_events']['fields']['capacity_c'] = [
    'name' => 'capacity_c',
    'vname' => 'LBL_CAPACITY',
    'type' => 'int',
    'len' => '11',
    'default' => 0,
    'studio' => 'visible',
];

$dictionary['FP_events']['fields']['seats_remaining_c'] = [
    'name' => 'seats_remaining_c',
    'vname' => 'LBL_SEATS_REMAINING',
    'type' => 'int',
    'len' => '11',
    'default' => 0,
    'readonly' => true,
    'studio' => 'visible',
];

$dictionary['FP_events']['fields']['registration_deadline_c'] = [
    'name' => 'registration_deadline_c',
    'vname' => 'LBL_REGISTRATION_DEADLINE',
    'type' => 'datetime',
    'studio' => 'visible',
];

$dictionary['FP_events']['fields']['is_public_c'] = [
    'name' => 'is_public_c',
    'vname' => 'LBL_IS_PUBLIC',
    'type' => 'bool',
    'default' => 1,
    'studio' => 'visible',
];

$dictionary['FP_events']['fields']['virtual_meeting_url_c'] = [
    'name' => 'virtual_meeting_url_c',
    'vname' => 'LBL_VIRTUAL_MEETING_URL',
    'type' => 'url',
    'len' => '255',
    'studio' => 'visible',
];

$dictionary['FP_events']['fields']['ev_ticket_types'] = [
    'name' => 'ev_ticket_types',
    'type' => 'link',
    'relationship' => 'fp_events_ev_ticket_types',
    'source' => 'non-db',
    'vname' => 'LBL_EV_TICKET_TYPES',
];

$dictionary['FP_events']['fields']['ev_registrations'] = [
    'name' => 'ev_registrations',
    'type' => 'link',
    'relationship' => 'fp_events_ev_registrations',
    'source' => 'non-db',
    'vname' => 'LBL_EV_REGISTRATIONS',
];

$dictionary['FP_events']['fields']['ev_event_seats'] = [
    'name' => 'ev_event_seats',
    'type' => 'link',
    'relationship' => 'fp_events_ev_event_seats',
    'source' => 'non-db',
    'vname' => 'LBL_EV_EVENT_SEATS',
];