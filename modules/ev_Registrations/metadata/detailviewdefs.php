<?php
$viewdefs['ev_Registrations']['DetailView'] = [
    'templateMeta' => [
        'form' => ['buttons' => ['EDIT', 'DUPLICATE', 'DELETE']],
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
            ['assigned_seats_summary', 'quantity'],
            ['contact_name', 'purchased_by_contact_name'],
            ['amount_paid', 'transaction_id'],
            ['badge_name', 'checked_in_at'],
            ['checkin_token', 'assigned_user_name'],
            ['dietary_restrictions'],
            ['description'],
        ],
    ],
];