<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

$subpanel_layout = [
    'top_buttons' => [
        ['widget_class' => 'SubPanelTopCreateButton'],
    ],
    'where' => '',
    'list_fields' => [
        'name' => [
            'vname' => 'LBL_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '20%',
        ],
        'contact_name' => [
            'vname' => 'LBL_CONTACT_NAME',
            'module' => 'Contacts',
            'widget_class' => 'SubPanelDetailViewLink',
            'target_record_key' => 'contact_id',
            'target_module' => 'Contacts',
            'width' => '25%',
        ],
        'ticket_type_name' => [
            'vname' => 'LBL_TICKET_TYPE_NAME',
            'module' => 'ev_TicketTypes',
            'widget_class' => 'SubPanelDetailViewLink',
            'target_record_key' => 'ticket_type_id',
            'target_module' => 'ev_TicketTypes',
            'width' => '20%',
        ],
        'quantity' => [
            'vname' => 'LBL_QUANTITY',
            'width' => '10%',
        ],
        'registration_status' => [
            'vname' => 'LBL_REGISTRATION_STATUS',
            'width' => '20%',
        ],
        'edit_button' => [
            'widget_class' => 'SubPanelEditButton',
            'module' => 'ev_Registrations',
            'width' => '5%',
        ],
    ],
];
