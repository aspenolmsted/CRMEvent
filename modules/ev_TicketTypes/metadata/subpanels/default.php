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
            'width' => '35%',
        ],
        'price' => [
            'vname' => 'LBL_PRICE',
            'width' => '20%',
        ],
        'quantity_available' => [
            'vname' => 'LBL_QUANTITY_AVAILABLE',
            'width' => '15%',
        ],
        'quantity_sold' => [
            'vname' => 'LBL_QUANTITY_SOLD',
            'width' => '15%',
        ],
        'status' => [
            'vname' => 'LBL_STATUS',
            'width' => '15%',
        ],
        'edit_button' => [
            'widget_class' => 'SubPanelEditButton',
            'module' => 'ev_TicketTypes',
            'width' => '4%',
        ],
    ],
];
