<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

class ev_EventSeats extends Basic
{
    public $new_schema = true;
    public $module_dir = 'ev_EventSeats';
    public $object_name = 'ev_EventSeats';
    public $table_name = 'ev_event_seats';
    public $importable = true;

    public $id;
    public $name;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $modified_by_name;
    public $created_by;
    public $created_by_name;
    public $description;
    public $deleted;
    public $assigned_user_id;
    public $assigned_user_name;

    public $event_id;
    public $event_name;
    public $venue_seat_id;
    public $venue_seat_name;
    public $ticket_type_id;
    public $ticket_type_name;
    public $registration_id;
    public $registration_name;
    public $section_name;
    public $seat_status;
    public $hold_expires_at;

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }
}
