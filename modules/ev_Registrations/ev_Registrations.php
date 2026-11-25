<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

class ev_Registrations extends Basic
{
    public $new_schema = true;
    public $module_dir = 'ev_Registrations';
    public $object_name = 'ev_Registrations';
    public $table_name = 'ev_registrations';
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
    public $ticket_type_id;
    public $ticket_type_name;
    public $event_seat_id;
    public $event_seat_name;
    public $contact_id;
    public $contact_name;
    public $purchased_by_contact_id;
    public $purchased_by_contact_name;
    public $registration_status;
    public $quantity;
    public $amount_paid;
    public $currency_id;
    public $transaction_id;
    public $checkin_token;
    public $checked_in_at;
    public $badge_name;
    public $dietary_restrictions;
    public $selected_seat_ids;
    public $assigned_seats_summary;

    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
        }
        return false;
    }
}
