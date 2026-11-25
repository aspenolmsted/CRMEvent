<?php
// modules/ev_VenueSeats/Menu.php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

global $mod_strings;

if (ACLController::checkAccess('ev_VenueSeats', 'edit', true)) {
    $module_menu[] = [
        'index.php?module=ev_VenueSeats&action=EditView&return_module=ev_VenueSeats&return_action=DetailView',
        $mod_strings['LNK_NEW_RECORD'],
        'Add',
        'ev_VenueSeats',
    ];
}

if (ACLController::checkAccess('ev_VenueSeats', 'list', true)) {
    $module_menu[] = [
        'index.php?module=ev_VenueSeats&action=index&return_module=ev_VenueSeats&return_action=DetailView',
        $mod_strings['LNK_LIST'],
        'List',
        'ev_VenueSeats',
    ];
}