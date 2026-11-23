<?php

$dictionary['FP_events']['fields']['capacity_c'] = [
    'name' => 'capacity_c',
    'vname' => 'LBL_CAPACITY',
    'type' => 'int',
    'len' => '11',
    'default' => 0,
];

$dictionary['FP_events']['fields']['seats_remaining_c'] = [
    'name' => 'seats_remaining_c',
    'vname' => 'LBL_SEATS_REMAINING',
    'type' => 'int',
    'len' => '11',
    'default' => 0,
    'readonly' => true,
];

$dictionary['FP_events']['fields']['registration_deadline_c'] = [
    'name' => 'registration_deadline_c',
    'vname' => 'LBL_REGISTRATION_DEADLINE',
    'type' => 'datetime',
];

$dictionary['FP_events']['fields']['is_public_c'] = [
    'name' => 'is_public_c',
    'vname' => 'LBL_IS_PUBLIC',
    'type' => 'bool',
    'default' => 1,
];

$dictionary['FP_events']['fields']['virtual_meeting_url_c'] = [
    'name' => 'virtual_meeting_url_c',
    'vname' => 'LBL_VIRTUAL_MEETING_URL',
    'type' => 'url',
    'len' => '255',
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
