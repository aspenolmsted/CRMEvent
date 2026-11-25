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
        'section_name' => [
            'vname' => 'LBL_SECTION_NAME',
            'width' => '25%',
        ],
        'seat_type' => [
            'vname' => 'LBL_SEAT_TYPE',
            'width' => '20%',
        ],
        'pos_x' => [
            'vname' => 'LBL_POS_X',
            'width' => '10%',
        ],
        'pos_y' => [
            'vname' => 'LBL_POS_Y',
            'width' => '10%',
        ],
    ],
];
