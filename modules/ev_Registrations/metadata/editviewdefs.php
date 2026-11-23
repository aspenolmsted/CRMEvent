<?php
$viewdefs['ev_Registrations']['EditView'] = [
    'templateMeta' => [
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [
        'default' => [
            ['name', 'registration_status'],
            ['event_name', 'ticket_type_name'],
            ['contact_name', 'purchased_by_contact_name'],
            ['quantity', 'amount_paid'],
            ['badge_name', 'transaction_id'],
            ['checked_in_at', 'assigned_user_name'],
            ['dietary_restrictions'],
            ['description'],
        ],
    ],
];
