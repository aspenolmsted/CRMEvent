<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$layout_defs['ev_Venues'] = [
    'subpanel_setup' => [
        'ev_venue_seats' => [
            'order' => 10,
            'module' => 'ev_VenueSeats',
            'subpanel_name' => 'default',
            'sort_order' => 'asc',
            'sort_by' => 'name',
            'title_key' => 'LBL_VENUE_SEATS_SUBPANEL_TITLE',
            'get_subpanel_data' => 'ev_venue_seats',
            'top_buttons' => [
                ['widget_class' => 'SubPanelTopCreateButton'],
            ],
        ],
    ],
];
