<?php

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

if (empty($this->mapData)) {
    echo '<div class="alert alert-warning">Event seat map unavailable.</div>';
    return;
}

$event = $this->mapData['event'];
$venueName = $this->mapData['venue_name'];
$ticketTypes = $this->mapData['ticket_types'];
$seatsJson = json_encode($this->mapData['seats']);
if ($seatsJson === false) {
    $seatsJson = '[]';
}
$seatsJson = str_replace('</', '<\/', $seatsJson);

$layoutJson = json_encode($this->mapData['layout']);
if ($layoutJson === false) {
    $layoutJson = '{"items":[]}';
}
$layoutJson = str_replace('</', '<\/', $layoutJson);
?>
<div class="com-crmevent-seatmap" style="max-width: 980px; margin: 0 auto; position: relative; z-index: 1;">
    <p>
        <a href="<?php echo Route::_('index.php?option=com_crmevent&view=events'); ?>">&larr; Back to Events</a>
    </p>

    <h2><?php echo htmlspecialchars($event['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
    <p style="color: #475569;">
        <?php if (!empty($venueName)) : ?>
            <strong>Venue:</strong> <?php echo htmlspecialchars($venueName, ENT_QUOTES, 'UTF-8'); ?> &nbsp;&bull;&nbsp;
        <?php endif; ?>
        <strong>Date:</strong> <?php echo htmlspecialchars($event['date_start'], ENT_QUOTES, 'UTF-8'); ?> &nbsp;&bull;&nbsp;
        <strong>Remaining Seats:</strong> <?php echo (int) ($event['seats_remaining_c'] ?? 0); ?>
    </p>

    <?php if (!empty($this->confirmation)) : ?>
        <div style="padding: 16px; margin-bottom: 20px; background: #ecfdf5; border: 1px solid #10b981; border-radius: 6px;">
            <h3 style="margin-top: 0; color: #065f46;">Registration Confirmed</h3>
            <p style="margin: 4px 0;"><strong>Confirmation Code:</strong> <?php echo htmlspecialchars($this->confirmation['name'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p style="margin: 4px 0;"><strong>Assigned Seats (<?php echo (int) $this->confirmation['quantity']; ?>):</strong> <?php echo htmlspecialchars($this->confirmation['assigned_seats_summary'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p style="margin: 4px 0;"><strong>Status:</strong> <?php echo htmlspecialchars($this->confirmation['registration_status'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    <?php endif; ?>

    <div style="margin-bottom: 10px; display: flex; gap: 18px; font-size: 13px; align-items: center;">
        <span><strong style="color: #10b981;">&#9679;</strong> Available</span>
        <span><strong style="color: #2563eb;">&#9679;</strong> Selected</span>
        <span><strong style="color: #f59e0b;">&#9679;</strong> Held</span>
        <span><strong style="color: #94a3b8;">&#9679;</strong> Reserved</span>
        <span id="seatMapDebugStatus" style="color: #64748b;">(<?php echo count($this->mapData['seats']); ?> total seats)</span>
    </div>

    <div style="border: 2px solid #cbd5e1; background: #ffffff; width: 100%; max-width: 960px; height: 560px; overflow: auto; border-radius: 6px; margin-bottom: 20px;">
        <svg id="joomlaSeatMap" width="960" height="560" style="display: block; max-width: none;"></svg>
    </div>

    <form action="<?php echo Route::_('index.php?option=com_crmevent&task=register'); ?>" method="post" id="crmeventRegForm" style="background: #f8fafc; padding: 20px; border: 1px solid #cbd5e1; border-radius: 6px;">
        <h3 style="margin-top: 0;">Attendee &amp; Seat Details</h3>

        <div id="selectedSeatBanner" style="padding: 10px 12px; background: #e2e8f0; border-radius: 4px; font-weight: bold; margin-bottom: 16px;">
            Selected Seats (0): Click one or more green seats on the map above
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div>
                <label for="ticket_type_id"><strong>Ticket Tier *</strong></label>
                <select name="ticket_type_id" id="ticket_type_id" class="form-select" required style="width: 100%; padding: 8px;" onchange="updateTotalDisplay();">
                    <?php foreach ($ticketTypes as $tt) : ?>
                        <option value="<?php echo htmlspecialchars($tt['id'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-price="<?php echo (float) $tt['price']; ?>">
                            <?php echo htmlspecialchars($tt['name'], ENT_QUOTES, 'UTF-8'); ?>
                            ($<?php echo number_format((float) $tt['price'], 2); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label><strong>Estimated Total</strong></label>
                <div id="totalAmountDisplay" style="padding: 8px; font-size: 16px; font-weight: bold; color: #0f172a;">$0.00</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div>
                <label for="first_name"><strong>First Name *</strong></label>
                <input type="text" name="first_name" id="first_name" class="form-control" required style="width: 100%; padding: 8px;" />
            </div>
            <div>
                <label for="last_name"><strong>Last Name *</strong></label>
                <input type="text" name="last_name" id="last_name" class="form-control" required style="width: 100%; padding: 8px;" />
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div>
                <label for="email"><strong>Email Address *</strong></label>
                <input type="email" name="email" id="email" class="form-control" required style="width: 100%; padding: 8px;" />
            </div>
            <div>
                <label for="phone"><strong>Phone</strong></label>
                <input type="text" name="phone" id="phone" class="form-control" style="width: 100%; padding: 8px;" />
            </div>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="dietary_restrictions"><strong>Accessibility / Special Notes</strong></label>
            <textarea name="dietary_restrictions" id="dietary_restrictions" rows="2" class="form-control" style="width: 100%; padding: 8px;"></textarea>
        </div>

        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event['id'], ENT_QUOTES, 'UTF-8'); ?>" />
        <input type="hidden" name="selected_seat_ids" id="selected_seat_ids" value="" />
        <input type="hidden" name="unit_price" id="unit_price" value="0" />
        <?php echo HTMLHelper::_('form.token'); ?>

        <button type="submit" class="btn btn-primary" id="submitRegBtn" disabled>Reserve Selected Seats</button>
    </form>

    <script type="application/json" id="joomla_seats_json"><?php echo $seatsJson; ?></script>
    <script type="application/json" id="joomla_layout_json"><?php echo $layoutJson; ?></script>
</div>

<script>
function initJoomlaSeatMap() {
    var statusEl = document.getElementById('seatMapDebugStatus');
    var svg = document.getElementById('joomlaSeatMap');
    if (!svg) return;

    var seats = [];
    var layout = { items: [] };

    try {
        var seatsScript = document.getElementById('joomla_seats_json');
        if (seatsScript && seatsScript.textContent) {
            seats = JSON.parse(seatsScript.textContent);
        }
        var layoutScript = document.getElementById('joomla_layout_json');
        if (layoutScript && layoutScript.textContent) {
            layout = JSON.parse(layoutScript.textContent);
        }
    } catch (err) {
        if (statusEl) statusEl.textContent = '(JSON error: ' + err.message + ')';
        return;
    }

    if (!Array.isArray(seats)) seats = [];
    if (!layout) layout = { items: [] };

    var selected = [];
    var circleMap = {};

    var maxX = 960;
    var maxY = 560;
    seats.forEach(function(s) {
        var sx = parseInt(s.pos_x, 10);
        if (isNaN(sx)) sx = 0;
        var sy = parseInt(s.pos_y, 10);
        if (isNaN(sy)) sy = 0;
        if (sx + 60 > maxX) maxX = sx + 60;
        if (sy + 60 > maxY) maxY = sy + 60;
    });
    if (Array.isArray(layout.items)) {
        layout.items.forEach(function(it) {
            var ix = parseInt(it.x, 10);
            if (isNaN(ix)) ix = 0;
            var iy = parseInt(it.y, 10);
            if (isNaN(iy)) iy = 0;
            if (ix + 160 > maxX) maxX = ix + 160;
            if (iy + 80 > maxY) maxY = iy + 80;
        });
    }
    svg.setAttribute('width', String(maxX));
    svg.setAttribute('height', String(maxY));

    window.updateTotalDisplay = function() {
        var select = document.getElementById('ticket_type_id');
        var price = 0;
        if (select && select.options.length > 0 && select.selectedIndex >= 0) {
            price = parseFloat(select.options[select.selectedIndex].getAttribute('data-price'));
            if (isNaN(price)) price = 0;
        }
        document.getElementById('unit_price').value = price.toFixed(2);
        var total = price * selected.length;
        document.getElementById('totalAmountDisplay').textContent = '$' + total.toFixed(2);
    };

    function refreshSelectionUI() {
        var banner = document.getElementById('selectedSeatBanner');
        var hiddenIds = document.getElementById('selected_seat_ids');
        var submitBtn = document.getElementById('submitRegBtn');

        if (selected.length === 0) {
            banner.textContent = 'Selected Seats (0): Click one or more green seats on the map above';
            hiddenIds.value = '';
            submitBtn.disabled = true;
        } else {
            banner.textContent = 'Selected Seats (' + selected.length + '): ' + selected.map(function(s) { return s.name; }).join(', ');
            hiddenIds.value = selected.map(function(s) { return s.id; }).join(',');
            submitBtn.disabled = false;
        }

        Object.keys(circleMap).forEach(function(id) {
            var active = selected.some(function(s) { return s.id === id; });
            circleMap[id].setAttribute('fill', active ? '#2563eb' : '#10b981');
            circleMap[id].setAttribute('r', active ? '12' : '10');
        });

        window.updateTotalDisplay();
    }

    if (Array.isArray(layout.items)) {
        layout.items.forEach(function(item) {
            var ix = parseInt(item.x, 10);
            if (isNaN(ix)) ix = 0;
            var iy = parseInt(item.y, 10);
            if (isNaN(iy)) iy = 0;

            var g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            g.setAttribute('transform', 'translate(' + ix + ',' + iy + ')');

            if (item.type === 'stage') {
                var rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                rect.setAttribute('x', '-120');
                rect.setAttribute('y', '-25');
                rect.setAttribute('width', '240');
                rect.setAttribute('height', '50');
                rect.setAttribute('rx', '6');
                rect.setAttribute('fill', '#1e293b');
                g.appendChild(rect);

                var txt = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                txt.setAttribute('text-anchor', 'middle');
                txt.setAttribute('dy', '5');
                txt.setAttribute('fill', '#ffffff');
                txt.setAttribute('font-size', '13');
                txt.setAttribute('font-weight', 'bold');
                txt.textContent = item.label ? item.label : 'STAGE';
                g.appendChild(txt);
            } else if (item.type === 'table') {
                var circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                circle.setAttribute('r', '24');
                circle.setAttribute('fill', '#f1f5f9');
                circle.setAttribute('stroke', '#94a3b8');
                circle.setAttribute('stroke-width', '1.5');
                g.appendChild(circle);

                var lbl = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                lbl.setAttribute('text-anchor', 'middle');
                lbl.setAttribute('dy', '4');
                lbl.setAttribute('font-size', '10');
                lbl.setAttribute('fill', '#475569');
                lbl.textContent = item.label ? item.label : '';
                g.appendChild(lbl);
            } else if (item.type === 'grid') {
                var title = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                title.setAttribute('text-anchor', 'middle');
                title.setAttribute('x', '0');
                title.setAttribute('y', '-18');
                title.setAttribute('font-size', '11');
                title.setAttribute('font-weight', 'bold');
                title.setAttribute('fill', '#334155');
                title.textContent = item.label ? item.label : '';
                g.appendChild(title);
            }
            svg.appendChild(g);
        });
    }

    seats.forEach(function(seat, idx) {
        var x = parseInt(seat.pos_x, 10);
        if (isNaN(x)) x = 0;
        if (x === 0) x = 60 + (idx % 15) * 50;

        var y = parseInt(seat.pos_y, 10);
        if (isNaN(y)) y = 0;
        if (y === 0) y = 60 + Math.floor(idx / 15) * 50;

        var isAvail = seat.seat_status === 'Available';

        var g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        g.setAttribute('transform', 'translate(' + x + ',' + y + ')');
        g.style.cursor = isAvail ? 'pointer' : 'not-allowed';

        var circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circle.setAttribute('r', '10');
        circle.setAttribute('fill', isAvail ? '#10b981' : (seat.seat_status === 'Held' ? '#f59e0b' : '#94a3b8'));
        circle.setAttribute('stroke', '#ffffff');
        circle.setAttribute('stroke-width', '1.5');
        g.appendChild(circle);

        var tip = document.createElementNS('http://www.w3.org/2000/svg', 'title');
        tip.textContent = seat.name + ' (' + seat.seat_status + ')';
        g.appendChild(tip);

        if (isAvail) {
            circleMap[seat.id] = circle;
            g.addEventListener('click', function() {
                var pos = selected.findIndex(function(s) { return s.id === seat.id; });
                if (pos >= 0) {
                    selected.splice(pos, 1);
                } else {
                    selected.push({ id: seat.id, name: seat.name });
                }
                refreshSelectionUI();
            });
        }

        svg.appendChild(g);
    });

    if (statusEl) {
        statusEl.textContent = '(' + seats.length + ' total seats rendered)';
    }

    refreshSelectionUI();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initJoomlaSeatMap);
} else {
    initJoomlaSeatMap();
}
</script>