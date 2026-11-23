<?php
$listViewDefs['ev_Registrations'] = [
    'NAME' => [
        'width' => '18%',
        'label' => 'LBL_NAME',
        'default' => true,
        'link' => true,
    ],
    'EVENT_NAME' => [
        'width' => '22%',
        'label' => 'LBL_EVENT_NAME',
        'id' => 'EVENT_ID',
        'module' => 'FP_events',
        'default' => true,
        'link' => true,
    ],
    'CONTACT_NAME' => [
        'width' => '20%',
        'label' => 'LBL_CONTACT_NAME',
        'id' => 'CONTACT_ID',
        'module' => 'Contacts',
        'default' => true,
        'link' => true,
    ],
    'TICKET_TYPE_NAME' => [
        'width' => '15%',
        'label' => 'LBL_TICKET_TYPE_NAME',
        'id' => 'TICKET_TYPE_ID',
        'module' => 'ev_TicketTypes',
        'default' => true,
        'link' => true,
    ],
    'QUANTITY' => [
        'width' => '10%',
        'label' => 'LBL_QUANTITY',
        'default' => true,
    ],
    'REGISTRATION_STATUS' => [
        'width' => '15%',
        'label' => 'LBL_REGISTRATION_STATUS',
        'default' => true,
    ],
];
