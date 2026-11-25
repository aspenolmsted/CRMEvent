<?php
$searchdefs['ev_Venues'] = [
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
            'address_city',
        ],
        'advanced_search' => [
            'name',
            'address_city',
            'address_state',
            'assigned_user_id',
        ],
    ],
];
