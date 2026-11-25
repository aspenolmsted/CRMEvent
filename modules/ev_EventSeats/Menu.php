<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $mod_strings;

if (ACLController::checkAccess('ev_EventSeats', 'list', true)) {
    $module_menu[] = [
        'index.php?module=ev_EventSeats&action=index&return_module=ev_EventSeats&return_action=DetailView',
        $mod_strings['LNK_LIST'],
        'List',
        'ev_EventSeats',
    ];
}
