<?php

$layout_defs['Contacts']['subpanel_setup']['ev_registrations'] = [
    'order' => 30,
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
