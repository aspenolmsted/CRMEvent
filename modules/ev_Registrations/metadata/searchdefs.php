<?php
$searchdefs['ev_Registrations'] = [
    'templateMeta' => [
        'maxColumns' => '3',
        'maxColumnsBasic' => '3',
        'widths' => [
            'label' => '10',
            'field' => '30',
        ],
    ],
    'layout' => [
        'basic_search' => [
            'name',
            'event_name',
            'contact_name',
            'registration_status',
        ],
        'advanced_search' => [
            'name',
            'event_name',
            'ticket_type_name',
            'contact_name',
            'registration_status',
            'transaction_id',
            'assigned_user_id',
        ],
    ],
];
