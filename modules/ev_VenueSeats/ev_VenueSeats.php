<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

class ev_VenueSeats extends Basic
{
    public $new_schema = true;
    public $module_dir = 'ev_VenueSeats';
    public $object_name = 'ev_VenueSeats';
    public $table_name = 'ev_venue_seats';
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

    public $venue_id;
    public $venue_name;
    public $section_name;
    public $row_label;
    public $seat_number;
    public $seat_type;
    public $pos_x;
    public $pos_y;

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }
}
