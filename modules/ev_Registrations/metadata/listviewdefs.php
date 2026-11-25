<?php
$listViewDefs['ev_Registrations'] = [
    'NAME' => [
        'width' => '16%',
        'label' => 'LBL_NAME',
        'default' => true,
        'link' => true,
    ],
    'EVENT_NAME' => [
        'width' => '20%',
        'label' => 'LBL_EVENT_NAME',
        'id' => 'EVENT_ID',
        'module' => 'FP_events',
        'default' => true,
        'link' => true,
    ],
    'CONTACT_NAME' => [
        'width' => '18%',
        'label' => 'LBL_CONTACT_NAME',
        'id' => 'CONTACT_ID',
        'module' => 'Contacts',
        'default' => true,
        'link' => true,
    ],
    'TICKET_TYPE_NAME' => [
        'width' => '14%',
        'label' => 'LBL_TICKET_TYPE_NAME',
        'id' => 'TICKET_TYPE_ID',
        'module' => 'ev_TicketTypes',
        'default' => true,
        'link' => true,
    ],
    'ASSIGNED_SEATS_SUMMARY' => [
        'width' => '16%',
        'label' => 'LBL_ASSIGNED_SEATS_SUMMARY',
        'default' => true,
    ],
    'REGISTRATION_STATUS' => [
        'width' => '16%',
        'label' => 'LBL_REGISTRATION_STATUS',
        'default' => true,
    ],
];