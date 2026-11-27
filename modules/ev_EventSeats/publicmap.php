<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

@ini_set('display_errors', '0');
while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

$eventId = isset($_GET['event_id']) ? preg_replace('/[^a-zA-Z0-9\-]/', '', (string) $_GET['event_id']) : '';
if ($eventId === '') {
    http_response_code(400);
    echo json_encode(array('error' => 'Missing event_id parameter.'));
    exit;
}

$db = DBManagerFactory::getInstance();
$escapedEventId = $db->quote($eventId);

$eventBean = BeanFactory::newBean('FP_events');
$venueBean = BeanFactory::newBean('ev_Venues');
$vsBean    = BeanFactory::newBean('ev_VenueSeats');
$esBean    = BeanFactory::newBean('ev_EventSeats');
$ttBean    = BeanFactory::newBean('ev_TicketTypes');

$eventTable = $eventBean ? $eventBean->table_name : 'fp_events';
$venueTable = $venueBean ? $venueBean->table_name : 'ev_venues';
$vsTable    = $vsBean ? $vsBean->table_name : 'ev_venue_seats';
$esTable    = $esBean ? $esBean->table_name : 'ev_event_seats';
$ttTable    = $ttBean ? $ttBean->table_name : 'ev_ticket_types';

$hasEventCstm = $db->tableExists($eventTable . '_cstm');

if ($hasEventCstm) {
    $eventSql = "
        SELECT
            e.id,
            e.name,
            e.date_start,
            e.date_end,
            e.description,
            ec.venue_id_c,
            ec.capacity_c,
            ec.seats_remaining_c,
            ec.is_public_c,
            v.name AS venue_name,
            v.layout_json
        FROM {$eventTable} e
        LEFT JOIN {$eventTable}_cstm ec ON ec.id_c = e.id
        LEFT JOIN {$venueTable} v ON v.id = ec.venue_id_c AND v.deleted = 0
        WHERE e.id = '{$escapedEventId}'
          AND e.deleted = 0
        LIMIT 1
    ";
} else {
    $eventSql = "
        SELECT
            e.id,
            e.name,
            e.date_start,
            e.date_end,
            e.description,
            '' AS venue_id_c,
            0 AS capacity_c,
            0 AS seats_remaining_c,
            '1' AS is_public_c,
            '' AS venue_name,
            '' AS layout_json
        FROM {$eventTable} e
        WHERE e.id = '{$escapedEventId}'
          AND e.deleted = 0
        LIMIT 1
    ";
}

$eventRes = $db->query($eventSql);
$eventRow = $eventRes ? $db->fetchByAssoc($eventRes) : null;

if (!$eventRow) {
    http_response_code(404);
    echo json_encode(array('error' => 'Event not found.'));
    exit;
}

$isPublic = isset($eventRow['is_public_c']) ? (string) $eventRow['is_public_c'] : '';
if ($isPublic !== '' && $isPublic !== '1') {
    http_response_code(403);
    echo json_encode(array('error' => 'Event is not open for public registration.'));
    exit;
}

$layout = array('items' => array());
if (!empty($eventRow['layout_json'])) {
    $rawLayout = html_entity_decode((string) $eventRow['layout_json'], ENT_QUOTES, 'UTF-8');
    $decodedLayout = json_decode($rawLayout, true);
    if (is_array($decodedLayout)) {
        $layout = $decodedLayout;
    }
}

$venueName = isset($eventRow['venue_name']) ? (string) $eventRow['venue_name'] : '';
unset($eventRow['layout_json'], $eventRow['venue_name']);

// 2. Fetch all EventSeats + VenueSeats coordinates in one JOIN
$seatsSql = "
    SELECT
        es.id,
        es.name,
        es.venue_seat_id,
        es.section_name,
        es.seat_status,
        COALESCE(vs.pos_x, 0) AS pos_x,
        COALESCE(vs.pos_y, 0) AS pos_y,
        COALESCE(NULLIF(vs.seat_type, ''), 'Standard') AS seat_type
    FROM {$esTable} es
    LEFT JOIN {$vsTable} vs ON vs.id = es.venue_seat_id AND vs.deleted = 0
    WHERE es.event_id = '{$escapedEventId}'
      AND es.deleted = 0
";

$seatsRes = $db->query($seatsSql);
$seats = array();
if ($seatsRes) {
    while ($row = $db->fetchByAssoc($seatsRes)) {
        $row['pos_x'] = (int) $row['pos_x'];
        $row['pos_y'] = (int) $row['pos_y'];
        $seats[] = $row;
    }
}

usort($seats, static function (array $a, array $b): int {
    return strnatcmp((string) $a['name'], (string) $b['name']);
});

// 3. Fetch Active Ticket Types for this Event
$ttSql = "
    SELECT
        id,
        name,
        price,
        quantity_available,
        quantity_sold,
        description
    FROM {$ttTable}
    WHERE event_id = '{$escapedEventId}'
      AND status = 'Active'
      AND deleted = 0
    ORDER BY price ASC
";

$ttRes = $db->query($ttSql);
$ticketTypes = array();
if ($ttRes) {
    while ($row = $db->fetchByAssoc($ttRes)) {
        $row['price'] = (float) $row['price'];
        $ticketTypes[] = $row;
    }
}

echo json_encode(array(
    'event'        => $eventRow,
    'venue_name'   => $venueName,
    'layout'       => $layout,
    'seats'        => $seats,
    'ticket_types' => $ticketTypes,
));
exit;