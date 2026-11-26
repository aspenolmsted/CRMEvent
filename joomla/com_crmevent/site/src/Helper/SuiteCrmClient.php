<?php

namespace Alliance\Component\Crmevent\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Http\HttpFactory;

class SuiteCrmClient
{
    private string $endpoint;
    private string $username;
    private string $passwordHash;
    private ?string $sessionId = null;

    public function __construct()
    {
        $params = ComponentHelper::getParams('com_crmevent');
        $baseUrl = rtrim((string) $params->get('suitecrm_url', ''), '/');
        $this->endpoint = $baseUrl . '/service/v4_1/rest.php';
        $this->username = (string) $params->get('suitecrm_user', '');
        $this->passwordHash = md5((string) $params->get('suitecrm_password', ''));
    }

    private function call(string $method, array $restData): array
    {
        if (empty($this->endpoint) || $this->endpoint === '/service/v4_1/rest.php') {
            throw new \RuntimeException('SuiteCRM URL is not configured in CRMEvent component options.');
        }

        $http = (new HttpFactory())->getHttp();
        $postFields = array(
            'method'        => $method,
            'input_type'    => 'JSON',
            'response_type' => 'JSON',
            'rest_data'     => json_encode($restData),
        );

        $response = $http->post($this->endpoint, $postFields);
        if ($response->code !== 200) {
            $mod = isset($restData['module_name']) ? ' [' . $restData['module_name'] . ' / ' . $method . ']' : ' [' . $method . ']';
            throw new \RuntimeException('SuiteCRM API HTTP error ' . $response->code . $mod);
        }

        $decoded = json_decode((string) $response->body, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid JSON returned by SuiteCRM API.');
        }

        if (isset($decoded['name'], $decoded['number']) && !isset($decoded['id']) && !isset($decoded['entry_list'])) {
            $mod = isset($restData['module_name']) ? ' [' . $restData['module_name'] . ' / ' . $method . ']' : ' [' . $method . ']';
            throw new \RuntimeException('SuiteCRM API error' . $mod . ': ' . ($decoded['description'] ?? $decoded['name']));
        }

        return $decoded;
    }

    public function login(): string
    {
        if ($this->sessionId !== null) {
            return $this->sessionId;
        }

        $res = $this->call('login', array(
            'user_auth' => array(
                'user_name' => $this->username,
                'password'  => $this->passwordHash,
            ),
            'application_name' => 'Joomla_CRMEvent',
        ));

        if (empty($res['id'])) {
            throw new \RuntimeException('Failed to authenticate with SuiteCRM REST API.');
        }

        $this->sessionId = (string) $res['id'];
        return $this->sessionId;
    }

    private function flattenEntryList(array $apiResponse): array
    {
        $rows = array();
        $entries = isset($apiResponse['entry_list']) ? $apiResponse['entry_list'] : array();
        foreach ($entries as $entry) {
            $item = array('id' => isset($entry['id']) ? $entry['id'] : '');
            $nvList = isset($entry['name_value_list']) ? $entry['name_value_list'] : array();
            foreach ($nvList as $field => $meta) {
                $item[$field] = isset($meta['value']) ? $meta['value'] : '';
            }
            $rows[] = $item;
        }
        return $rows;
    }

    private function fetchAllEntries(string $moduleName, array $selectFields, int $batchSize = 500): array
    {
        $session = $this->login();
        $all = array();
        $offset = 0;

        while (true) {
            $res = $this->call('get_entry_list', array(
                'session'                   => $session,
                'module_name'               => $moduleName,
                'query'                     => '',
                'order_by'                  => '',
                'offset'                    => $offset,
                'select_fields'             => $selectFields,
                'link_name_to_fields_array' => array(),
                'max_results'               => $batchSize,
                'deleted'                   => 0,
            ));

            $batch = $this->flattenEntryList($res);
            foreach ($batch as $row) {
                $all[] = $row;
            }

            if (count($batch) < $batchSize) {
                break;
            }
            $offset += $batchSize;
        }

        return $all;
    }

    private function findEntryById(string $moduleName, string $recordId, array $selectFields): ?array
    {
        foreach ($this->fetchAllEntries($moduleName, $selectFields, 200) as $row) {
            if (($row['id'] ?? '') === $recordId) {
                return $row;
            }
        }
        return null;
    }

    public function getPublicEvents(): array
    {
        $events = $this->fetchAllEntries('FP_events', array(
            'id',
            'name',
            'date_start',
            'date_end',
            'description',
            'venue_id_c',
            'capacity_c',
            'seats_remaining_c',
            'registration_deadline_c',
            'is_public_c',
        ), 100);

        $filtered = array_values(array_filter($events, static function (array $ev): bool {
            return !isset($ev['is_public_c']) || $ev['is_public_c'] === '' || (string) $ev['is_public_c'] === '1';
        }));

        usort($filtered, static function (array $a, array $b): int {
            return strcmp((string) ($a['date_start'] ?? ''), (string) ($b['date_start'] ?? ''));
        });

        return $filtered;
    }

    public function getEventSeatMapData(string $eventId): array
    {
        $safeEventId = preg_replace('/[^a-zA-Z0-9\-]/', '', $eventId);

        // 1. Fetch Event details
        $event = $this->findEntryById(
            'FP_events',
            $safeEventId,
            array('id', 'name', 'date_start', 'description', 'venue_id_c', 'capacity_c', 'seats_remaining_c')
        );
        if (!$event || empty($event['id'])) {
            throw new \RuntimeException('Event not found in SuiteCRM.');
        }

        // 2. Fetch Venue layout_json (decode HTML entities returned by SuiteCRM REST API)
        $layout = array('items' => array());
        $venueName = '';
        $safeVenueId = '';
        if (!empty($event['venue_id_c'])) {
            $safeVenueId = preg_replace('/[^a-zA-Z0-9\-]/', '', $event['venue_id_c']);
            $venue = $this->findEntryById(
                'ev_Venues',
                $safeVenueId,
                array('id', 'name', 'layout_json')
            );
            if ($venue !== null) {
                $venueName = isset($venue['name']) ? $venue['name'] : '';
                $rawLayout = html_entity_decode((string) (isset($venue['layout_json']) ? $venue['layout_json'] : ''), ENT_QUOTES, 'UTF-8');
                $decodedLayout = json_decode($rawLayout, true);
                if (is_array($decodedLayout)) {
                    $layout = $decodedLayout;
                }
            }
        }

        // 3. Fetch ALL VenueSeats across pages so multi-venue seats never truncate
        $coordMap = array();
        if ($safeVenueId !== '') {
            $allVenueSeats = $this->fetchAllEntries(
                'ev_VenueSeats',
                array('id', 'venue_id', 'pos_x', 'pos_y', 'seat_type'),
                500
            );
            foreach ($allVenueSeats as $vs) {
                if (($vs['venue_id'] ?? '') === $safeVenueId) {
                    $coordMap[$vs['id']] = $vs;
                }
            }
        }

        // 4. Fetch ALL EventSeats and merge coordinates
        $allEventSeats = $this->fetchAllEntries(
            'ev_EventSeats',
            array('id', 'name', 'event_id', 'venue_seat_id', 'section_name', 'seat_status'),
            500
        );

        $seats = array();
        foreach ($allEventSeats as $es) {
            if (($es['event_id'] ?? '') !== $safeEventId) {
                continue;
            }
            $vsId = isset($es['venue_seat_id']) ? $es['venue_seat_id'] : '';
            $vs = isset($coordMap[$vsId]) ? $coordMap[$vsId] : array();
            $es['pos_x'] = (int) (isset($vs['pos_x']) ? $vs['pos_x'] : 0);
            $es['pos_y'] = (int) (isset($vs['pos_y']) ? $vs['pos_y'] : 0);
            $es['seat_type'] = isset($vs['seat_type']) ? $vs['seat_type'] : 'Standard';
            $seats[] = $es;
        }

        usort($seats, static function (array $a, array $b): int {
            return strnatcmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
        });

        // 5. Fetch Active Ticket Types for this Event
        $allTicketTypes = $this->fetchAllEntries(
            'ev_TicketTypes',
            array('id', 'name', 'event_id', 'status', 'price', 'quantity_available', 'quantity_sold', 'description'),
            200
        );
        $ticketTypes = array();
        foreach ($allTicketTypes as $tt) {
            if (($tt['event_id'] ?? '') === $safeEventId && ($tt['status'] ?? '') === 'Active') {
                $ticketTypes[] = $tt;
            }
        }

        usort($ticketTypes, static function (array $a, array $b): int {
            return (float) ($a['price'] ?? 0) <=> (float) ($b['price'] ?? 0);
        });

        return array(
            'event'        => $event,
            'venue_name'   => $venueName,
            'layout'       => $layout,
            'seats'        => $seats,
            'ticket_types' => $ticketTypes,
        );
    }

    public function findOrCreateContact(string $firstName, string $lastName, string $email, string $phone = ''): string
    {
        $session = $this->login();
        $cleanEmail = trim($email);

        $searchRes = $this->call('search_by_module', array(
            'session'             => $session,
            'search_string'       => $cleanEmail,
            'modules'             => array('Contacts'),
            'offset'              => 0,
            'max_results'         => 5,
            'assigned_user_id'    => '',
            'select_fields'       => array('id', 'first_name', 'last_name', 'email1'),
            'unified_search_only' => false,
            'favorites'           => false,
        ));

        $entryList = isset($searchRes['entry_list']) ? $searchRes['entry_list'] : array();
        foreach ($entryList as $modBlock) {
            $records = isset($modBlock['records']) ? $modBlock['records'] : array();
            foreach ($records as $rec) {
                $id = isset($rec['id']['value']) ? $rec['id']['value'] : '';
                $recEmail = isset($rec['email1']['value']) ? $rec['email1']['value'] : '';
                if ($id !== '' && strcasecmp($recEmail, $cleanEmail) === 0) {
                    return (string) $id;
                }
            }
        }

        $createRes = $this->call('set_entry', array(
            'session'         => $session,
            'module_name'     => 'Contacts',
            'name_value_list' => array(
                array('name' => 'first_name', 'value' => trim($firstName)),
                array('name' => 'last_name', 'value' => trim($lastName)),
                array('name' => 'email1', 'value' => $cleanEmail),
                array('name' => 'phone_work', 'value' => trim($phone)),
            ),
        ));

        if (empty($createRes['id'])) {
            throw new \RuntimeException('Unable to create Contact record in SuiteCRM.');
        }

        return (string) $createRes['id'];
    }

    public function createMultiSeatRegistration(array $payload): array
    {
        $session = $this->login();
        $params = ComponentHelper::getParams('com_crmevent');
        $defaultStatus = (string) $params->get('default_reg_status', 'Confirmed');

        $rawSeatIds = isset($payload['seat_ids']) ? $payload['seat_ids'] : array();
        $seatIds = array_values(array_filter(array_map('trim', $rawSeatIds)));
        if (empty($seatIds)) {
            throw new \RuntimeException('Please select at least one seat.');
        }

        $safeEventId = preg_replace('/[^a-zA-Z0-9\-]/', '', $payload['event_id']);
        $allEventSeats = $this->fetchAllEntries(
            'ev_EventSeats',
            array('id', 'name', 'event_id', 'seat_status'),
            500
        );

        $liveSeats = array();
        foreach ($allEventSeats as $row) {
            if (($row['event_id'] ?? '') === $safeEventId) {
                $liveSeats[$row['id']] = $row;
            }
        }

        $seatNames = array();
        foreach ($seatIds as $sid) {
            if (!isset($liveSeats[$sid])) {
                throw new \RuntimeException('One or more selected seats could not be found.');
            }
            if ($liveSeats[$sid]['seat_status'] !== 'Available') {
                throw new \RuntimeException('Seat ' . $liveSeats[$sid]['name'] . ' is no longer available. Please choose another seat.');
            }
            $seatNames[] = $liveSeats[$sid]['name'];
        }

        $contactId = $this->findOrCreateContact(
            $payload['first_name'],
            $payload['last_name'],
            $payload['email'],
            isset($payload['phone']) ? $payload['phone'] : ''
        );

        $quantity = count($seatIds);
        $unitPrice = (float) (isset($payload['unit_price']) ? $payload['unit_price'] : 0);
        $totalAmount = round($unitPrice * $quantity, 2);

        $regRes = $this->call('set_entry', array(
            'session'         => $session,
            'module_name'     => 'ev_Registrations',
            'name_value_list' => array(
                array('name' => 'event_id', 'value' => $safeEventId),
                array('name' => 'ticket_type_id', 'value' => isset($payload['ticket_type_id']) ? $payload['ticket_type_id'] : ''),
                array('name' => 'contact_id', 'value' => $contactId),
                array('name' => 'purchased_by_contact_id', 'value' => $contactId),
                array('name' => 'event_seat_id', 'value' => $seatIds[0]),
                array('name' => 'selected_seat_ids', 'value' => implode(',', $seatIds)),
                array('name' => 'assigned_seats_summary', 'value' => implode(', ', $seatNames)),
                array('name' => 'quantity', 'value' => $quantity),
                array('name' => 'amount_paid', 'value' => $totalAmount),
                array('name' => 'registration_status', 'value' => $defaultStatus),
                array('name' => 'badge_name', 'value' => trim($payload['first_name'] . ' ' . $payload['last_name'])),
                array('name' => 'dietary_restrictions', 'value' => trim(isset($payload['dietary_restrictions']) ? $payload['dietary_restrictions'] : '')),
            ),
        ));

        if (empty($regRes['id'])) {
            throw new \RuntimeException('SuiteCRM failed to save the registration record.');
        }

        $savedRow = $this->findEntryById(
            'ev_Registrations',
            (string) $regRes['id'],
            array('id', 'name', 'checkin_token', 'assigned_seats_summary', 'quantity', 'amount_paid', 'registration_status')
        );

        if ($savedRow !== null) {
            return $savedRow;
        }

        return array(
            'id'                     => $regRes['id'],
            'name'                   => $regRes['id'],
            'assigned_seats_summary' => implode(', ', $seatNames),
            'quantity'               => $quantity,
            'amount_paid'            => $totalAmount,
            'registration_status'    => $defaultStatus,
        );
    }
}