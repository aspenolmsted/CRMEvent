<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $mod_strings;

if (ACLController::checkAccess('ev_Venues', 'edit', true)) {
    $module_menu[] = [
        'index.php?module=ev_Venues&action=EditView&return_module=ev_Venues&return_action=DetailView',
        $mod_strings['LNK_NEW_RECORD'],
        'Add',
        'ev_Venues',
    ];
}

if (ACLController::checkAccess('ev_Venues', 'list', true)) {
    $module_menu[] = [
        'index.php?module=ev_Venues&action=index&return_module=ev_Venues&return_action=DetailView',
        $mod_strings['LNK_LIST'],
        'List',
        'ev_Venues',
    ];
}
