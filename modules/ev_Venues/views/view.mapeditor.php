<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/MVC/View/SugarView.php');

class ev_VenuesViewMapeditor extends SugarView
{
    public function display()
    {
        $recordId = $_REQUEST['record'] ?? '';
        $venue = BeanFactory::getBean('ev_Venues', $recordId);
        if (!$venue || empty($venue->id)) {
            echo '<div class="alert alert-danger">Venue not found.</div>';
            return;
        }

        $venueName = htmlspecialchars($venue->name, ENT_QUOTES, 'UTF-8');
        $venueId = htmlspecialchars($venue->id, ENT_QUOTES, 'UTF-8');
        $layoutJson = !empty($venue->layout_json) ? $venue->layout_json : '{"items":[]}';
        $safeJson = htmlspecialchars($layoutJson, ENT_QUOTES, 'UTF-8');
        ?>
<div style="padding: 15px;">
    <h2>Seating Map Designer: <?php echo $venueName; ?></h2>
    <p>Add round banquet tables, angled or curved row sections, or stage landmarks, drag them into position on the floor plan, and click <strong>Save Seating Map &amp; Generate Seats</strong>.</p>

    <div style="margin-bottom: 12px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center; background: #f5f7fa; padding: 10px; border: 1px solid #d9e1e8; border-radius: 4px;">
        <label>Label: <input type="text" id="itemLabel" value="Left Orchestra" style="width: 115px; padding: 4px;" /></label>
        <label>Start Row: <input type="text" id="itemStartRow" value="A" maxlength="2" style="width: 42px; padding: 4px; text-transform: uppercase;" /></label>
        <label>Seats / Cols: <input type="number" id="itemCols" value="8" min="1" max="40" style="width: 55px; padding: 4px;" /></label>
        <label>Rows: <input type="number" id="itemRows" value="4" min="1" max="26" style="width: 50px; padding: 4px;" /></label>
        <label>Angle (&deg;): <input type="number" id="itemAngle" value="0" min="-180" max="180" step="5" style="width: 55px; padding: 4px;" title="Tilt angle in degrees (e.g. 20 for Left Orchestra, -20 for Right Orchestra)" /></label>
        <label>Curve (&deg;): <input type="number" id="itemCurve" value="0" min="0" max="120" step="5" style="width: 55px; padding: 4px;" title="Arc spread in degrees (e.g. 30 for curved Center Orchestra)" /></label>
        <label>Tier:
            <select id="itemSeatType" style="padding: 4px;">
                <option value="Standard">Standard</option>
                <option value="VIP">VIP</option>
                <option value="Accessible">Accessible (ADA)</option>
                <option value="Obstructed">Obstructed</option>
            </select>
        </label>
        <button type="button" class="button" onclick="addRowGrid()">+ Add Row Section</button>
        <button type="button" class="button" onclick="addBanquetTable()">+ Add Banquet Table</button>
        <button type="button" class="button" onclick="addStage()">+ Add Stage / Podium</button>
        <button type="button" class="button" onclick="clearMap()" style="color: #a94442;">Clear Canvas</button>
    </div>

    <div style="border: 2px solid #cbd5e1; background: #ffffff; width: 900px; height: 560px; position: relative; overflow: hidden; border-radius: 4px;">
        <svg id="venueCanvas" width="900" height="560" style="cursor: grab; user-select: none;"></svg>
    </div>

    <form method="POST" action="index.php" style="margin-top: 15px;" onsubmit="prepareSubmit()">
        <input type="hidden" name="module" value="ev_Venues" />
        <input type="hidden" name="action" value="savemap" />
        <input type="hidden" name="record" value="<?php echo $venueId; ?>" />
        <input type="hidden" id="layout_json_input" name="layout_json" value="<?php echo $safeJson; ?>" />
        <input type="submit" class="button primary" value="Save Seating Map &amp; Generate Seats" />
        <a href="index.php?module=ev_Venues&amp;action=DetailView&amp;record=<?php echo $venueId; ?>" class="button">Cancel</a>
    </form>
</div>

<script>
(function() {
    var raw = document.getElementById('layout_json_input').value;
    var ta = document.createElement('textarea');
    ta.innerHTML = raw;

    var state = { items: [] };
    try {
        var parsed = JSON.parse(ta.value);
        if (parsed && Array.isArray(parsed.items)) {
            state = parsed;
        }
    } catch (e) {}

    var svg = document.getElementById('venueCanvas');
    var draggingIndex = null;
    var offsetX = 0, offsetY = 0;
    var itemCounter = state.items.length + 1;

    function getCanvasCoords(evt) {
        var rect = svg.getBoundingClientRect();
        return {
            x: evt.clientX - rect.left,
            y: evt.clientY - rect.top
        };
    }

    function rowIndexToLabel(startLabel, offset) {
        var clean = (startLabel || 'A').toUpperCase().replace(/[^A-Z]/g, '') || 'A';
        var num = 0;
        for (var i = 0; i < clean.length; i++) {
            num = num * 26 + (clean.charCodeAt(i) - 64);
        }
        num += offset;
        var out = '';
        while (num > 0) {
            var rem = (num - 1) % 26;
            out = String.fromCharCode(65 + rem) + out;
            num = Math.floor((num - 1) / 26);
        }
        return out;
    }

    function computeGridSeatPositions(item) {
        var rows = item.rows || 3;
        var cols = item.cols || 8;
        var startRow = item.start_row || 'A';
        var angleDeg = parseFloat(item.angle) || 0;
        var curveDeg = parseFloat(item.curve) || 0;
        var angleRad = (angleDeg * Math.PI) / 180;
        var spacing = 28;
        var seats = [];

        if (curveDeg > 0 && cols > 1) {
            var curveRad = (curveDeg * Math.PI) / 180;
            var baseRadius = ((cols - 1) * spacing) / curveRad;
            for (var r = 0; r < rows; r++) {
                var rLabel = rowIndexToLabel(startRow, r);
                var radius = baseRadius + r * spacing;
                for (var c = 0; c < cols; c++) {
                    var t = (c / (cols - 1)) - 0.5;
                    var theta = t * curveRad;
                    var localX = radius * Math.sin(theta);
                    var localY = (radius * Math.cos(theta)) - baseRadius;
                    var rotX = localX * Math.cos(angleRad) - localY * Math.sin(angleRad);
                    var rotY = localX * Math.sin(angleRad) + localY * Math.cos(angleRad);
                    seats.push({
                        row: rLabel,
                        col: c + 1,
                        dx: Math.round(rotX),
                        dy: Math.round(rotY)
                    });
                }
            }
        } else {
            var halfW = ((cols - 1) * spacing) / 2;
            for (var r = 0; r < rows; r++) {
                var rLabel = rowIndexToLabel(startRow, r);
                for (var c = 0; c < cols; c++) {
                    var localX = (c * spacing) - halfW;
                    var localY = r * spacing;
                    var rotX = localX * Math.cos(angleRad) - localY * Math.sin(angleRad);
                    var rotY = localX * Math.sin(angleRad) + localY * Math.cos(angleRad);
                    seats.push({
                        row: rLabel,
                        col: c + 1,
                        dx: Math.round(rotX),
                        dy: Math.round(rotY)
                    });
                }
            }
        }
        return seats;
    }

    function render() {
        while (svg.firstChild) {
            svg.removeChild(svg.firstChild);
        }

        state.items.forEach(function(item, idx) {
            var g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            g.setAttribute('transform', 'translate(' + item.x + ',' + item.y + ')');
            g.style.cursor = 'move';

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
                circle.setAttribute('r', 26);
                circle.setAttribute('fill', item.seat_type === 'VIP' ? '#fef3c7' : '#e0f2fe');
                circle.setAttribute('stroke', item.seat_type === 'VIP' ? '#d97706' : '#0284c7');
                circle.setAttribute('stroke-width', '2');
                g.appendChild(circle);

                var seats = item.seats || 8;
                for (var s = 0; s < seats; s++) {
                    var angle = (2 * Math.PI * s) / seats;
                    var sc = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    sc.setAttribute('cx', Math.round(38 * Math.cos(angle)));
                    sc.setAttribute('cy', Math.round(38 * Math.sin(angle)));
                    sc.setAttribute('r', 7);
                    sc.setAttribute('fill', '#10b981');
                    g.appendChild(sc);
                }

                var lbl = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                lbl.setAttribute('text-anchor', 'middle');
                lbl.setAttribute('dy', '4');
                lbl.setAttribute('font-size', '11');
                lbl.setAttribute('font-weight', 'bold');
                lbl.textContent = item.label;
                g.appendChild(lbl);
            } else if (item.type === 'grid') {
                var computedSeats = computeGridSeatPositions(item);
                var startRow = (item.start_row || 'A').toUpperCase();
                var endRow = rowIndexToLabel(startRow, (item.rows || 3) - 1);

                var title = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                title.setAttribute('text-anchor', 'middle');
                title.setAttribute('x', 0);
                title.setAttribute('y', -18);
                title.setAttribute('font-size', '11');
                title.setAttribute('font-weight', 'bold');
                title.setAttribute('fill', '#1e293b');
                title.textContent = item.label + ' [Rows ' + startRow + '-' + endRow + ']';
                g.appendChild(title);

                computedSeats.forEach(function(st) {
                    var seatDot = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    seatDot.setAttribute('cx', st.dx);
                    seatDot.setAttribute('cy', st.dy);
                    seatDot.setAttribute('r', 9);
                    seatDot.setAttribute('fill', item.seat_type === 'VIP' ? '#f59e0b' : '#10b981');
                    seatDot.setAttribute('stroke', '#ffffff');
                    seatDot.setAttribute('stroke-width', '1');

                    var tip = document.createElementNS('http://www.w3.org/2000/svg', 'title');
                    tip.textContent = item.label + ' ' + st.row + '-' + st.col;
                    seatDot.appendChild(tip);
                    g.appendChild(seatDot);

                    if (st.col === 1) {
                        var rowTxt = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                        rowTxt.setAttribute('x', st.dx - 16);
                        rowTxt.setAttribute('y', st.dy + 3);
                        rowTxt.setAttribute('font-size', '9');
                        rowTxt.setAttribute('fill', '#475569');
                        rowTxt.textContent = st.row;
                        g.appendChild(rowTxt);
                    }
                });
            }

            g.addEventListener('mousedown', function(evt) {
                evt.preventDefault();
                var pt = getCanvasCoords(evt);
                draggingIndex = idx;
                offsetX = pt.x - item.x;
                offsetY = pt.y - item.y;
            });

            svg.appendChild(g);
        });
    }

    window.addEventListener('mousemove', function(evt) {
        if (draggingIndex === null) return;
        var pt = getCanvasCoords(evt);
        state.items[draggingIndex].x = Math.max(40, Math.min(860, Math.round(pt.x - offsetX)));
        state.items[draggingIndex].y = Math.max(40, Math.min(520, Math.round(pt.y - offsetY)));
        render();
    });

    window.addEventListener('mouseup', function() {
        draggingIndex = null;
    });

    window.addBanquetTable = function() {
        var lbl = document.getElementById('itemLabel').value || ('Table ' + itemCounter);
        var seats = parseInt(document.getElementById('itemCols').value, 10) || 8;
        var seatType = document.getElementById('itemSeatType').value;
        state.items.push({
            type: 'table',
            label: lbl,
            seats: seats,
            seat_type: seatType,
            x: 160 + ((itemCounter * 45) % 520),
            y: 220
        });
        itemCounter++;
        document.getElementById('itemLabel').value = 'Table ' + itemCounter;
        render();
    };

    window.addRowGrid = function() {
        var lbl = document.getElementById('itemLabel').value || ('Section ' + itemCounter);
        var startRow = (document.getElementById('itemStartRow').value || 'A').toUpperCase().replace(/[^A-Z]/g, '') || 'A';
        var cols = parseInt(document.getElementById('itemCols').value, 10) || 8;
        var rows = parseInt(document.getElementById('itemRows').value, 10) || 4;
        var angle = parseFloat(document.getElementById('itemAngle').value) || 0;
        var curve = parseFloat(document.getElementById('itemCurve').value) || 0;
        var seatType = document.getElementById('itemSeatType').value;

        state.items.push({
            type: 'grid',
            label: lbl,
            start_row: startRow,
            rows: rows,
            cols: cols,
            angle: angle,
            curve: curve,
            seat_type: seatType,
            x: 450,
            y: 160
        });
        itemCounter++;
        document.getElementById('itemLabel').value = 'Section ' + itemCounter;
        render();
    };

    window.addStage = function() {
        state.items.push({
            type: 'stage',
            label: 'MAIN STAGE',
            x: 450,
            y: 45
        });
        render();
    };

    window.clearMap = function() {
        if (confirm('Clear all tables and sections from this venue map?')) {
            state.items = [];
            itemCounter = 1;
            render();
        }
    };

    window.prepareSubmit = function() {
        document.getElementById('layout_json_input').value = JSON.stringify(state);
    };

    render();
})();
</script>
        <?php
    }
}