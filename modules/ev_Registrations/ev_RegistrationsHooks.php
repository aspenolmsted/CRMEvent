<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

class ev_RegistrationsHooks
{
    public function beforeSaveRegistration($bean, $event, $arguments)
    {
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
    }

    public function afterSaveRegistration($bean, $event, $arguments)
    {
        $this->recalculateTicketAndEventCounts($bean->ticket_type_id, $bean->event_id);
        $this->syncNativeEventAttendee($bean);
    }

    public function afterDeleteRegistration($bean, $event, $arguments)
    {
        $this->recalculateTicketAndEventCounts($bean->ticket_type_id, $bean->event_id);
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
