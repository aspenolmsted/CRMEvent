<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

class ev_RegistrationsHooks
{
    public function beforeSaveRegistration($bean, $event, $arguments)
    {
        if (!empty($bean->ticket_type_id)) {
            $ticketBean = BeanFactory::getBean('ev_TicketTypes', $bean->ticket_type_id);
            if ($ticketBean && !empty($ticketBean->id) && !empty($ticketBean->event_id) && $ticketBean->event_id !== $bean->event_id) {
                SugarApplication::appendErrorMessage('The selected Ticket Type does not belong to the selected Event.');
                throw new Exception('Invalid Ticket Type for selected Event.');
            }
        }
        if (empty($bean->checkin_token)) {
            $bean->checkin_token = bin2hex(random_bytes(16));
        }

        if (empty($bean->name)) {
            $bean->name = 'EVT-' . date('Y') . '-' . strtoupper(substr($bean->checkin_token, 0, 6));
        }

        if ($bean->registration_status === 'Checked_In' && empty($bean->checked_in_at)) {
            global $timedate;
            $bean->checked_in_at = $timedate->nowDb();
        }

        // Capture selected_seat_ids from POST if dynamically injected by Seat Picker
        if (!empty($_POST['selected_seat_ids'])) {
            $bean->selected_seat_ids = trim($_POST['selected_seat_ids']);
        }

        // Build readable assigned_seats_summary across all chosen seats
        $seatIds = $this->parseSeatIds($bean);
        if (!empty($seatIds)) {
            $labels = [];
            foreach ($seatIds as $sid) {
                $seatBean = BeanFactory::getBean('ev_EventSeats', $sid);
                if ($seatBean && !empty($seatBean->name)) {
                    $labels[] = $seatBean->name;
                }
            }
            if (!empty($labels)) {
                $bean->assigned_seats_summary = substr(implode(', ', $labels), 0, 255);
            }
        }
    }

    public function afterSaveRegistration($bean, $event, $arguments)
    {
        $this->recalculateTicketAndEventCounts($bean->ticket_type_id, $bean->event_id);
        $this->syncNativeEventAttendee($bean);
        $this->syncAssignedSeats($bean);
    }

    public function afterDeleteRegistration($bean, $event, $arguments)
    {
        $this->recalculateTicketAndEventCounts($bean->ticket_type_id, $bean->event_id);
        $this->releaseSeatsByRegistration($bean->id, $this->parseSeatIds($bean));
    }

    protected function parseSeatIds($bean): array
    {
        $ids = [];
        if (!empty($bean->selected_seat_ids)) {
            foreach (explode(',', $bean->selected_seat_ids) as $rawId) {
                $clean = trim($rawId);
                if ($clean !== '') {
                    $ids[$clean] = true;
                }
            }
        }
        if (!empty($bean->event_seat_id)) {
            $ids[trim($bean->event_seat_id)] = true;
        }
        return array_keys($ids);
    }

    protected function syncAssignedSeats($bean)
    {
        global $db;
        $seatIds = $this->parseSeatIds($bean);

        // Release any previously attached seats for this registration that were unselected
        if (!empty($bean->id) && $db->tableExists('ev_event_seats')) {
            $rid = $db->quote($bean->id);
            $res = $db->query("SELECT id FROM ev_event_seats WHERE registration_id = '{$rid}' AND deleted = 0");
            while ($row = $db->fetchByAssoc($res)) {
                if (!in_array($row['id'], $seatIds, true)) {
                    $oldSeat = BeanFactory::getBean('ev_EventSeats', $row['id']);
                    if ($oldSeat && !empty($oldSeat->id)) {
                        $oldSeat->seat_status = 'Available';
                        $oldSeat->registration_id = '';
                        $oldSeat->hold_expires_at = null;
                        $oldSeat->save(false);
                    }
                }
            }
        }

        foreach ($seatIds as $sid) {
            $seatBean = BeanFactory::getBean('ev_EventSeats', $sid);
            if (!$seatBean || empty($seatBean->id)) {
                continue;
            }

            if ($bean->registration_status === 'Cancelled') {
                $seatBean->seat_status = 'Available';
                $seatBean->registration_id = '';
                $seatBean->hold_expires_at = null;
            } elseif ($bean->registration_status === 'Pending') {
                $seatBean->seat_status = 'Held';
                $seatBean->registration_id = $bean->id;
            } else {
                $seatBean->seat_status = 'Reserved';
                $seatBean->registration_id = $bean->id;
                $seatBean->hold_expires_at = null;
            }
            $seatBean->save(false);
        }
    }

    protected function releaseSeatsByRegistration($registrationId, array $fallbackSeatIds = [])
    {
        global $db;
        $toRelease = [];

        if (!empty($registrationId) && $db->tableExists('ev_event_seats')) {
            $rid = $db->quote($registrationId);
            $res = $db->query("SELECT id FROM ev_event_seats WHERE registration_id = '{$rid}' AND deleted = 0");
            while ($row = $db->fetchByAssoc($res)) {
                $toRelease[$row['id']] = true;
            }
        }

        foreach ($fallbackSeatIds as $sid) {
            $toRelease[$sid] = true;
        }

        foreach (array_keys($toRelease) as $sid) {
            $seatBean = BeanFactory::getBean('ev_EventSeats', $sid);
            if ($seatBean && !empty($seatBean->id)) {
                $seatBean->seat_status = 'Available';
                $seatBean->registration_id = '';
                $seatBean->hold_expires_at = null;
                $seatBean->save(false);
            }
        }
    }

    protected function recalculateTicketAndEventCounts($ticketTypeId, $eventId)
    {
        global $db;

        if (!empty($ticketTypeId)) {
            $tid = $db->quote($ticketTypeId);
            $res = $db->query("SELECT COALESCE(SUM(quantity), 0) AS sold FROM ev_registrations WHERE ticket_type_id = '{$tid}' AND registration_status IN ('Confirmed', 'Checked_In') AND deleted = 0");
            $row = $db->fetchByAssoc($res);
            $sold = (int) ($row['sold'] ?? 0);

            $ticketBean = BeanFactory::getBean('ev_TicketTypes', $ticketTypeId);
            if ($ticketBean && !empty($ticketBean->id)) {
                $ticketBean->quantity_sold = $sold;
                if ((int) $ticketBean->quantity_available > 0 && $sold >= (int) $ticketBean->quantity_available && $ticketBean->status === 'Active') {
                    $ticketBean->status = 'Sold_Out';
                }
                $ticketBean->save(false);
            }
        }

        if (!empty($eventId)) {
            $eid = $db->quote($eventId);
            $res = $db->query("SELECT COALESCE(SUM(quantity), 0) AS total_reg FROM ev_registrations WHERE event_id = '{$eid}' AND registration_status IN ('Confirmed', 'Checked_In') AND deleted = 0");
            $row = $db->fetchByAssoc($res);
            $totalReg = (int) ($row['total_reg'] ?? 0);

            $eventBean = BeanFactory::getBean('FP_events', $eventId);
            if ($eventBean && !empty($eventBean->id)) {
                $capacity = (int) ($eventBean->capacity_c ?? 0);
                $eventBean->seats_remaining_c = max(0, $capacity - $totalReg);
                $eventBean->save(false);
            }
        }
    }

    protected function syncNativeEventAttendee($bean)
    {
        if (empty($bean->event_id) || empty($bean->contact_id)) {
            return;
        }

        if (!in_array($bean->registration_status, ['Confirmed', 'Checked_In'], true)) {
            return;
        }

        $eventBean = BeanFactory::getBean('FP_events', $bean->event_id);
        if ($eventBean && $eventBean->load_relationship('fp_events_contacts')) {
            $eventBean->fp_events_contacts->add($bean->contact_id, [
                'invite_status' => 'Invited',
                'accept_status' => 'Accepted',
            ]);
        }
    }
}