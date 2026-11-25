<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$subpanel_layout = [
    'top_buttons' => [],
    'where' => '',
    'list_fields' => [
        'name' => [
            'vname' => 'LBL_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '30%',
        ],
        'section_name' => [
            'vname' => 'LBL_SECTION_NAME',
            'width' => '20%',
        ],
        'ticket_type_name' => [
            'vname' => 'LBL_TICKET_TYPE_NAME',
            'module' => 'ev_TicketTypes',
            'widget_class' => 'SubPanelDetailViewLink',
            'target_record_key' => 'ticket_type_id',
            'target_module' => 'ev_TicketTypes',
            'width' => '20%',
        ],
        'seat_status' => [
            'vname' => 'LBL_SEAT_STATUS',
            'width' => '15%',
        ],
        'registration_name' => [
            'vname' => 'LBL_REGISTRATION_NAME',
            'module' => 'ev_Registrations',
            'widget_class' => 'SubPanelDetailViewLink',
            'target_record_key' => 'registration_id',
            'target_module' => 'ev_Registrations',
            'width' => '15%',
        ],
    ],
];
