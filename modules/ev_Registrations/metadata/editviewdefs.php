<?php
$viewdefs['ev_Registrations']['EditView'] = [
    'templateMeta' => [
        'form' => [
            'buttons' => [
                'SAVE',
                'CANCEL',
                [
                    'customCode' => '<input type="button" class="button" value="Visual Seat Picker" onclick="var f=document.forms[\'EditView\'];var eid=(f&&f.event_id)?f.event_id.value:(document.getElementById(\'event_id\')?document.getElementById(\'event_id\').value:\'\');if(!eid)return alert(\'Select an Event first.\');window.open(\'index.php?module=ev_EventSeats&action=seatpicker&event_id=\'+encodeURIComponent(eid),\'SeatPicker\',\'width=960,height=680,scrollbars=yes\');" />',
                ],
            ],
        ],
        'maxColumns' => '2',
        'widths' => [
            ['label' => '10', 'field' => '30'],
            ['label' => '10', 'field' => '30'],
        ],
    ],
    'panels' => [
        'default' => [
            [
                'event_name',
                [
                    'name' => 'ticket_type_name',
                    'label' => 'LBL_TICKET_TYPE_NAME',
                    'customCode' => '<input type="text" name="ticket_type_name" id="ticket_type_name" class="sqsEnabled" value="{$fields.ticket_type_name.value}" autocomplete="off" /><input type="hidden" name="ticket_type_id" id="ticket_type_id" value="{$fields.ticket_type_id.value}" /><span class="id-ff multiple"><button type="button" name="btn_ticket_type_name" id="btn_ticket_type_name" title="Select" class="button firstChild" onclick="openTicketTypePopupForEvent();"><span class="suitepicon suitepicon-action-select"></span></button><button type="button" name="btn_clr_ticket_type_name" id="btn_clr_ticket_type_name" title="Clear Selection" class="button lastChild" onclick="SUGAR.clearRelateField(this.form, \'ticket_type_name\', \'ticket_type_id\');"><span class="suitepicon suitepicon-action-clear"></span></button></span>',
                ],
            ],
            [
                [
                    'name' => 'assigned_seats_summary',
                    'label' => 'LBL_ASSIGNED_SEATS_SUMMARY',
                    'customCode' => '<input type="text" id="assigned_seats_summary" name="assigned_seats_summary" value="{$fields.assigned_seats_summary.value}" readonly="readonly" placeholder="Use Visual Seat Picker to select seats" style="background:#f8fafc; width:100%;" /><input type="hidden" name="event_seat_id" id="event_seat_id" value="{$fields.event_seat_id.value}" /><input type="hidden" name="selected_seat_ids" id="selected_seat_ids" value="{$fields.selected_seat_ids.value}" />',
                ],
                'quantity',
            ],
            ['contact_name', 'purchased_by_contact_name'],
            ['registration_status', 'amount_paid'],
            ['badge_name', 'transaction_id'],
            ['checked_in_at', 'assigned_user_name'],
            ['dietary_restrictions'],
            ['description'],
        ],
    ],
];