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
            'width' => '18%',
        ],
        'contact_name' => [
            'vname' => 'LBL_CONTACT_NAME',
            'module' => 'Contacts',
            'widget_class' => 'SubPanelDetailViewLink',
            'target_record_key' => 'contact_id',
            'target_module' => 'Contacts',
            'width' => '22%',
        ],
        'ticket_type_name' => [
            'vname' => 'LBL_TICKET_TYPE_NAME',
            'module' => 'ev_TicketTypes',
            'widget_class' => 'SubPanelDetailViewLink',
            'target_record_key' => 'ticket_type_id',
            'target_module' => 'ev_TicketTypes',
            'width' => '18%',
        ],
        'event_seat_name' => [
            'vname' => 'LBL_EVENT_SEAT_NAME',
            'module' => 'ev_EventSeats',
            'widget_class' => 'SubPanelDetailViewLink',
            'target_record_key' => 'event_seat_id',
            'target_module' => 'ev_EventSeats',
            'width' => '18%',
        ],
        'registration_status' => [
            'vname' => 'LBL_REGISTRATION_STATUS',
            'width' => '18%',
        ],
        'edit_button' => [
            'widget_class' => 'SubPanelEditButton',
            'module' => 'ev_Registrations',
            'width' => '6%',
        ],
    ],
];
