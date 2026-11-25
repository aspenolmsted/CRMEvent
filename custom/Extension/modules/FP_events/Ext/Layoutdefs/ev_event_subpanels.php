<?php

$layout_defs['FP_events']['subpanel_setup']['ev_ticket_types'] = [
    'order' => 10,
    'module' => 'ev_TicketTypes',
    'subpanel_name' => 'default',
    'sort_order' => 'asc',
    'sort_by' => 'name',
    'title_key' => 'LBL_EV_TICKET_TYPES_SUBPANEL_TITLE',
    'get_subpanel_data' => 'ev_ticket_types',
    'top_buttons' => [
        ['widget_class' => 'SubPanelTopCreateButton'],
    ],
];

$layout_defs['FP_events']['subpanel_setup']['ev_registrations'] = [
    'order' => 20,
    'module' => 'ev_Registrations',
    'subpanel_name' => 'default',
    'sort_order' => 'desc',
    'sort_by' => 'date_entered',
    'title_key' => 'LBL_EV_REGISTRATIONS_SUBPANEL_TITLE',
    'get_subpanel_data' => 'ev_registrations',
    'top_buttons' => [
        ['widget_class' => 'SubPanelTopCreateButton'],
    ],
];

$layout_defs['FP_events']['subpanel_setup']['ev_event_seats'] = [
    'order' => 30,
    'module' => 'ev_EventSeats',
    'subpanel_name' => 'default',
    'sort_order' => 'asc',
    'sort_by' => 'name',
    'title_key' => 'LBL_EV_EVENT_SEATS_SUBPANEL_TITLE',
    'get_subpanel_data' => 'ev_event_seats',
    'top_buttons' => [],
];
