<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $mod_strings;

if (ACLController::checkAccess('ev_Registrations', 'edit', true)) {
    $module_menu[] = [
        'index.php?module=ev_Registrations&action=EditView&return_module=ev_Registrations&return_action=DetailView',
        $mod_strings['LNK_NEW_RECORD'],
        'Add',
        'ev_Registrations',
    ];
}

if (ACLController::checkAccess('ev_Registrations', 'list', true)) {
    $module_menu[] = [
        'index.php?module=ev_Registrations&action=index&return_module=ev_Registrations&return_action=DetailView',
        $mod_strings['LNK_LIST'],
        'List',
        'ev_Registrations',
    ];
}
