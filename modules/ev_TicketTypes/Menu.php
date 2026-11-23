<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $mod_strings;

if (ACLController::checkAccess('ev_TicketTypes', 'edit', true)) {
    $module_menu[] = [
        'index.php?module=ev_TicketTypes&action=EditView&return_module=ev_TicketTypes&return_action=DetailView',
        $mod_strings['LNK_NEW_RECORD'],
        'Add',
        'ev_TicketTypes',
    ];
}

if (ACLController::checkAccess('ev_TicketTypes', 'list', true)) {
    $module_menu[] = [
        'index.php?module=ev_TicketTypes&action=index&return_module=ev_TicketTypes&return_action=DetailView',
        $mod_strings['LNK_LIST'],
        'List',
        'ev_TicketTypes',
    ];
}
