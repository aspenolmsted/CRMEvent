<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/views/view.edit.php');

class ev_RegistrationsViewEdit extends ViewEdit
{
    public function display()
    {
        parent::display();
        echo <<<'HTML'
<script>
function openTicketTypePopupForEvent() {
    var form = document.forms['EditView'];
    var eid = (form && form.event_id) ? form.event_id.value : (document.getElementById('event_id') ? document.getElementById('event_id').value : '');
    if (!eid) {
        alert('Please select an Event first.');
        return;
    }
    open_popup(
        'ev_TicketTypes',
        600,
        400,
        '&query=true&event_id_advanced=' + encodeURIComponent(eid),
        true,
        false,
        {
            call_back_function: 'set_return',
            form_name: 'EditView',
            field_to_name_array: {
                id: 'ticket_type_id',
                name: 'ticket_type_name'
            }
        },
        'single',
        true
    );
}

(function() {
    var lastEventId = '';

    function syncTicketTypeSqsFilter() {
        var form = document.forms['EditView'];
        if (!form || !form.event_id) return;

        var eid = form.event_id.value || '';

        if (lastEventId !== '' && eid !== lastEventId) {
            if (form.ticket_type_id) form.ticket_type_id.value = '';
            if (form.ticket_type_name) form.ticket_type_name.value = '';
            if (form.event_seat_id) form.event_seat_id.value = '';
            if (form.selected_seat_ids) form.selected_seat_ids.value = '';
            if (form.assigned_seats_summary) form.assigned_seats_summary.value = '';
        }
        lastEventId = eid;

        if (typeof sqs_objects !== 'undefined' && sqs_objects['EditView_ticket_type_name']) {
            var conditions = [
                { name: 'name', op: 'like_custom', end: '%', value: '' }
            ];
            if (eid) {
                conditions.push({ name: 'event_id', op: 'equals', value: eid });
                sqs_objects['EditView_ticket_type_name'].group = 'and';
            }
            sqs_objects['EditView_ticket_type_name'].conditions = conditions;
        }
    }

    setInterval(syncTicketTypeSqsFilter, 400);
})();
</script>
HTML;
    }
}