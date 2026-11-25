<?php
$viewdefs['ev_Venues']['DetailView'] = [
    'templateMeta' => [
        'form' => [
            'buttons' => [
                'EDIT',
                'DUPLICATE',
                'DELETE',
                [
                    'customCode' => '<input type="button" class="button primary" value="Design Seating Map" onclick="location.href=\'index.php?module=ev_Venues&action=MapEditor&record=\' + document.forms[\'DetailView\'].record.value;" />',
                ],
            ],
        ],
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [
        'default' => [
            ['name', 'total_capacity'],
            ['address_street', 'address_city'],
            ['address_state', 'address_postalcode'],
            ['assigned_user_name'],
            ['description'],
        ],
    ],
];