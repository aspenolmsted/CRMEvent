<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

class ev_EventVenueHooks
{
    public function syncVenueSeatsToEvent($bean, $event, $arguments)
    {
        if (empty($bean->id) || empty($bean->venue_id_c)) {
            return;
        }

        global $db, $current_user;

        if (!$db->tableExists('ev_event_seats') || !$db->tableExists('ev_venue_seats')) {
            return;
        }

        $eid = $db->quote($bean->id);
        $vid = $db->quote($bean->venue_id_c);

        // Skip cloning if event seats already exist for this event
        $checkRes = $db->query("SELECT COUNT(*) AS cnt FROM ev_event_seats WHERE event_id = '{$eid}' AND deleted = 0");
        $checkRow = $db->fetchByAssoc($checkRes);
        if ((int) ($checkRow['cnt'] ?? 0) > 0) {
            return;
        }

        // Load all template seats for the selected venue
        $seatRes = $db->query("SELECT id, name, section_name, row_label, seat_number, seat_type FROM ev_venue_seats WHERE venue_id = '{$vid}' AND deleted = 0 ORDER BY section_name ASC, row_label ASC, seat_number ASC");
        $count = 0;
        $ownerId = !empty($bean->assigned_user_id) ? $bean->assigned_user_id : ($current_user->id ?? '1');

        while ($row = $db->fetchByAssoc($seatRes)) {
            $eventSeat = BeanFactory::newBean('ev_EventSeats');
            $eventSeat->name = $row['name'];
            $eventSeat->event_id = $bean->id;
            $eventSeat->venue_seat_id = $row['id'];
            $eventSeat->section_name = $row['section_name'];
            $eventSeat->seat_status = 'Available';
            $eventSeat->assigned_user_id = $ownerId;
            $eventSeat->save();
            $count++;
        }

        // Update event capacity & remaining seats if seats were cloned and capacity was unset
        $currentCap = (int) ($bean->capacity_c ?? 0);
        if ($count > 0 && $currentCap === 0) {
            $db->query("UPDATE fp_events SET capacity_c = {$count}, seats_remaining_c = {$count} WHERE id = '{$eid}'");
        }
    }
}