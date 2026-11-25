<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class ev_EventSeatsViewSeatpicker extends SugarView
{
    public function display()
    {
        global $db;

        $eventId = $_REQUEST['event_id'] ?? '';
        $event = BeanFactory::getBean('FP_events', $eventId);
        if (!$event || empty($event->id)) {
            echo '<div class="alert alert-warning" style="padding:15px;">Please select an Event first before opening the visual seat picker.</div>';
            return;
        }

        $eid = $db->quote($event->id);
        $res = $db->query("
            SELECT es.id, es.name, es.section_name, es.seat_status, vs.pos_x, vs.pos_y, vs.seat_type
            FROM ev_event_seats es
            LEFT JOIN ev_venue_seats vs ON es.venue_seat_id = vs.id
            WHERE es.event_id = '{$eid}' AND es.deleted = 0
            ORDER BY es.name ASC
        ");

        $seats = [];
        while ($row = $db->fetchByAssoc($res)) {
            $seats[] = $row;
        }

        $layoutJson = '{"items":[]}';
        if (!empty($event->venue_id_c)) {
            $venue = BeanFactory::getBean('ev_Venues', $event->venue_id_c);
            if ($venue && !empty($venue->layout_json)) {
                $layoutJson = $venue->layout_json;
            }
        }

        $seatsJson = htmlspecialchars(json_encode($seats), ENT_QUOTES, 'UTF-8');
        $safeLayout = htmlspecialchars($layoutJson, ENT_QUOTES, 'UTF-8');
        $eventName = htmlspecialchars($event->name, ENT_QUOTES, 'UTF-8');
        ?>
<div style="padding: 15px; font-family: Arial, sans-serif;">
    <h3 style="margin-top: 0;">Select Seats for: <?php echo $eventName; ?></h3>
    <div style="margin-bottom: 10px; display: flex; gap: 15px; font-size: 13px; align-items: center;">
        <span><strong style="color: #10b981;">&#9679;</strong> Available</span>
        <span><strong style="color: #2563eb;">&#9679;</strong> Selected</span>
        <span><strong style="color: #f59e0b;">&#9679;</strong> Held</span>
        <span><strong style="color: #94a3b8;">&#9679;</strong> Reserved / Blocked</span>
        <span style="color: #64748b;">(<?php echo count($seats); ?> total seats)</span>
    </div>

    <div style="margin-bottom: 10px; padding: 10px 12px; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
        <div id="selectionSummary" style="font-size: 13px; font-weight: bold; color: #1e293b;">
            Selected (0): None — click one or more green seats below
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="button" class="button" onclick="clearSelectedSeats()">Clear</button>
            <button type="button" class="button primary" onclick="confirmSelectedSeats()">Confirm Selected Seats</button>
        </div>
    </div>

    <input type="hidden" id="picker_seats_data" value="<?php echo $seatsJson; ?>" />
    <input type="hidden" id="picker_layout_data" value="<?php echo $safeLayout; ?>" />
    <div style="border: 2px solid #cbd5e1; background: #ffffff; width: 900px; height: 520px; overflow: auto; border-radius: 4px;">
        <svg id="seatPickerCanvas" width="900" height="520"></svg>
    </div>
</div>
<script>
(function() {
    function decodeJsonInput(id, fallback) {
        var el = document.getElementById(id);
        if (!el) return fallback;
        var ta = document.createElement('textarea');
        ta.innerHTML = el.value;
        try {
            return JSON.parse(ta.value) || fallback;
        } catch (e) {
            return fallback;
        }
    }

    var seats = decodeJsonInput('picker_seats_data', []);
    var layout = decodeJsonInput('picker_layout_data', { items: [] });
    var svg = document.getElementById('seatPickerCanvas');
    var selectedSeats = [];
    var circleMap = {};

    if (window.opener && !window.opener.closed) {
        var doc = window.opener.document;
        var existingCsvEl = doc.getElementById('selected_seat_ids') || (doc.forms['EditView'] && doc.forms['EditView'].selected_seat_ids);
        var singleIdEl = doc.getElementById('event_seat_id') || (doc.forms['EditView'] && doc.forms['EditView'].event_seat_id);
        var preIds = [];
        if (existingCsvEl && existingCsvEl.value) {
            preIds = existingCsvEl.value.split(',').map(function(s) { return s.trim(); }).filter(Boolean);
        } else if (singleIdEl && singleIdEl.value) {
            preIds = [singleIdEl.value.trim()];
        }
        preIds.forEach(function(pid) {
            var match = seats.find(function(st) { return st.id === pid; });
            if (match && match.seat_status === 'Available') {
                selectedSeats.push({ id: match.id, name: match.name });
            }
        });
    }

    function updateSummary() {
        var summaryEl = document.getElementById('selectionSummary');
        if (selectedSeats.length === 0) {
            summaryEl.textContent = 'Selected (0): None — click one or more green seats below';
        } else {
            var names = selectedSeats.map(function(s) { return s.name; }).join(', ');
            summaryEl.textContent = 'Selected (' + selectedSeats.length + '): ' + names;
        }

        Object.keys(circleMap).forEach(function(seatId) {
            var isSel = selectedSeats.some(function(s) { return s.id === seatId; });
            circleMap[seatId].setAttribute('fill', isSel ? '#2563eb' : '#10b981');
            circleMap[seatId].setAttribute('r', isSel ? 12 : 10);
        });
    }

    if (layout && Array.isArray(layout.items)) {
        layout.items.forEach(function(item) {
            var g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            g.setAttribute('transform', 'translate(' + (item.x || 0) + ',' + (item.y || 0) + ')');

            if (item.type === 'stage') {
                var rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                rect.setAttribute('x', -120);
                rect.setAttribute('y', -25);
                rect.setAttribute('width', 240);
                rect.setAttribute('height', 50);
                rect.setAttribute('rx', 6);
                rect.setAttribute('fill', '#1e293b');
                g.appendChild(rect);

                var txt = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                txt.setAttribute('text-anchor', 'middle');
                txt.setAttribute('dy', '5');
                txt.setAttribute('fill', '#ffffff');
                txt.setAttribute('font-size', '13');
                txt.setAttribute('font-weight', 'bold');
                txt.textContent = item.label || 'STAGE';
                g.appendChild(txt);
            } else if (item.type === 'table') {
                var circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                circle.setAttribute('r', 24);
                circle.setAttribute('fill', '#f1f5f9');
                circle.setAttribute('stroke', '#94a3b8');
                circle.setAttribute('stroke-width', '1.5');
                g.appendChild(circle);

                var lbl = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                lbl.setAttribute('text-anchor', 'middle');
                lbl.setAttribute('dy', '4');
                lbl.setAttribute('font-size', '10');
                lbl.setAttribute('fill', '#475569');
                lbl.textContent = item.label || '';
                g.appendChild(lbl);
            } else if (item.type === 'grid') {
                var title = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                title.setAttribute('text-anchor', 'middle');
                title.setAttribute('x', 0);
                title.setAttribute('y', -18);
                title.setAttribute('font-size', '11');
                title.setAttribute('font-weight', 'bold');
                title.setAttribute('fill', '#334155');
                title.textContent = item.label || '';
                g.appendChild(title);
            }
            svg.appendChild(g);
        });
    }

    if (seats.length === 0) {
        var emptyTxt = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        emptyTxt.setAttribute('x', 450);
        emptyTxt.setAttribute('y', 250);
        emptyTxt.setAttribute('text-anchor', 'middle');
        emptyTxt.setAttribute('fill', '#64748b');
        emptyTxt.setAttribute('font-size', '14');
        emptyTxt.textContent = 'No seats found for this event. Ensure a Venue with generated seats is attached to the Event and re-save the Event.';
        svg.appendChild(emptyTxt);
        return;
    }

    seats.forEach(function(seat, idx) {
        var x = parseInt(seat.pos_x, 10) || (60 + (idx % 15) * 50);
        var y = parseInt(seat.pos_y, 10) || (60 + Math.floor(idx / 15) * 50);
        var isAvail = seat.seat_status === 'Available';

        var g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        g.setAttribute('transform', 'translate(' + x + ',' + y + ')');
        g.style.cursor = isAvail ? 'pointer' : 'not-allowed';

        var circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circle.setAttribute('r', 10);
        circle.setAttribute('fill', isAvail ? '#10b981' : (seat.seat_status === 'Held' ? '#f59e0b' : '#94a3b8'));
        circle.setAttribute('stroke', '#ffffff');
        circle.setAttribute('stroke-width', '1.5');
        g.appendChild(circle);

        var title = document.createElementNS('http://www.w3.org/2000/svg', 'title');
        title.textContent = seat.name + ' (' + seat.seat_status + ')';
        g.appendChild(title);

        if (isAvail) {
            circleMap[seat.id] = circle;
            g.addEventListener('click', function() {
                var existingIdx = selectedSeats.findIndex(function(s) { return s.id === seat.id; });
                if (existingIdx >= 0) {
                    selectedSeats.splice(existingIdx, 1);
                } else {
                    selectedSeats.push({ id: seat.id, name: seat.name });
                }
                updateSummary();
            });
        }

        svg.appendChild(g);
    });

    window.clearSelectedSeats = function() {
        selectedSeats = [];
        updateSummary();
    };

    window.confirmSelectedSeats = function() {
        if (selectedSeats.length === 0) {
            alert('Please click at least one green seat on the map.');
            return;
        }
        if (window.opener && !window.opener.closed) {
            var doc = window.opener.document;
            var form = doc.forms['EditView'];
            var idField = doc.getElementById('event_seat_id') || (form && form.event_seat_id);
            var nameField = doc.getElementById('event_seat_name') || (form && form.event_seat_name);
            var summaryField = doc.getElementById('assigned_seats_summary') || (form && form.assigned_seats_summary);
            var qtyField = doc.getElementById('quantity') || (form && form.quantity);
            var csvField = doc.getElementById('selected_seat_ids') || (form && form.selected_seat_ids);

            var allNames = selectedSeats.map(function(s) { return s.name; }).join(', ');
            if (idField) idField.value = selectedSeats[0].id;
            if (nameField) nameField.value = selectedSeats[0].name;
            if (summaryField) summaryField.value = allNames;
            if (qtyField) qtyField.value = selectedSeats.length;

            if (!csvField && form) {
                csvField = doc.createElement('input');
                csvField.type = 'hidden';
                csvField.name = 'selected_seat_ids';
                csvField.id = 'selected_seat_ids';
                form.appendChild(csvField);
            }
            if (csvField) {
                csvField.value = selectedSeats.map(function(s) { return s.id; }).join(',');
            }

            window.close();
        }
    };

    updateSummary();
})();
</script>
        <?php
    }
}