<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

class ev_VenuesController extends SugarController
{
    public function action_mapeditor()
    {
        $this->view = 'mapeditor';
    }

    public function action_savemap()
    {
        global $db, $current_user;

        $venueId = $_REQUEST['record'] ?? '';
        $layoutJson = $_REQUEST['layout_json'] ?? '{"items":[]}';
        $layoutJson = html_entity_decode($layoutJson, ENT_QUOTES, 'UTF-8');

        $venue = BeanFactory::getBean('ev_Venues', $venueId);
        if (!$venue || empty($venue->id)) {
            SugarApplication::redirect('index.php?module=ev_Venues&action=index');
            return;
        }

        $decoded = json_decode($layoutJson, true);
        if (!is_array($decoded) || !isset($decoded['items'])) {
            $decoded = ['items' => []];
        }

        $venue->layout_json = json_encode($decoded);

        $vid = $db->quote($venue->id);
        $db->query("UPDATE ev_venue_seats SET deleted = 1 WHERE venue_id = '{$vid}'");

        $totalSeats = 0;
        foreach ($decoded['items'] as $item) {
            $type = $item['type'] ?? '';
            $label = trim($item['label'] ?? 'Section');
            $x = (int) ($item['x'] ?? 100);
            $y = (int) ($item['y'] ?? 100);
            $seatType = $item['seat_type'] ?? 'Standard';

            if ($type === 'table') {
                $seatsCount = max(1, (int) ($item['seats'] ?? 8));
                $radius = 38;
                for ($s = 1; $s <= $seatsCount; $s++) {
                    $angle = (2 * M_PI * ($s - 1)) / $seatsCount;
                    $sx = (int) round($x + $radius * cos($angle));
                    $sy = (int) round($y + $radius * sin($angle));

                    $seat = BeanFactory::newBean('ev_VenueSeats');
                    $seat->name = "{$label} - Seat {$s}";
                    $seat->venue_id = $venue->id;
                    $seat->section_name = $label;
                    $seat->row_label = $label;
                    $seat->seat_number = (string) $s;
                    $seat->seat_type = $seatType;
                    $seat->pos_x = $sx;
                    $seat->pos_y = $sy;
                    $seat->assigned_user_id = $current_user->id;
                    $seat->save();
                    $totalSeats++;
                }
            } elseif ($type === 'grid') {
                $rows = max(1, (int) ($item['rows'] ?? 4));
                $cols = max(1, (int) ($item['cols'] ?? 8));
                $startRow = strtoupper(preg_replace('/[^A-Za-z]/', '', $item['start_row'] ?? 'A')) ?: 'A';
                $angleDeg = (float) ($item['angle'] ?? 0);
                $curveDeg = (float) ($item['curve'] ?? 0);
                $angleRad = deg2rad($angleDeg);
                $spacing = 28;

                if ($curveDeg > 0 && $cols > 1) {
                    $curveRad = deg2rad($curveDeg);
                    $baseRadius = (($cols - 1) * $spacing) / $curveRad;
                    for ($r = 0; $r < $rows; $r++) {
                        $rowLabel = $this->rowIndexToLabel($startRow, $r);
                        $radius = $baseRadius + $r * $spacing;
                        for ($c = 1; $c <= $cols; $c++) {
                            $t = (($c - 1) / ($cols - 1)) - 0.5;
                            $theta = $t * $curveRad;
                            $localX = $radius * sin($theta);
                            $localY = ($radius * cos($theta)) - $baseRadius;
                            $sx = (int) round($x + ($localX * cos($angleRad) - $localY * sin($angleRad)));
                            $sy = (int) round($y + ($localX * sin($angleRad) + $localY * cos($angleRad)));

                            $seat = BeanFactory::newBean('ev_VenueSeats');
                            $seat->name = "{$label} {$rowLabel}-{$c}";
                            $seat->venue_id = $venue->id;
                            $seat->section_name = $label;
                            $seat->row_label = $rowLabel;
                            $seat->seat_number = (string) $c;
                            $seat->seat_type = $seatType;
                            $seat->pos_x = $sx;
                            $seat->pos_y = $sy;
                            $seat->assigned_user_id = $current_user->id;
                            $seat->save();
                            $totalSeats++;
                        }
                    }
                } else {
                    $halfW = (($cols - 1) * $spacing) / 2;
                    for ($r = 0; $r < $rows; $r++) {
                        $rowLabel = $this->rowIndexToLabel($startRow, $r);
                        for ($c = 1; $c <= $cols; $c++) {
                            $localX = (($c - 1) * $spacing) - $halfW;
                            $localY = $r * $spacing;
                            $sx = (int) round($x + ($localX * cos($angleRad) - $localY * sin($angleRad)));
                            $sy = (int) round($y + ($localX * sin($angleRad) + $localY * cos($angleRad)));

                            $seat = BeanFactory::newBean('ev_VenueSeats');
                            $seat->name = "{$label} {$rowLabel}-{$c}";
                            $seat->venue_id = $venue->id;
                            $seat->section_name = $label;
                            $seat->row_label = $rowLabel;
                            $seat->seat_number = (string) $c;
                            $seat->seat_type = $seatType;
                            $seat->pos_x = $sx;
                            $seat->pos_y = $sy;
                            $seat->assigned_user_id = $current_user->id;
                            $seat->save();
                            $totalSeats++;
                        }
                    }
                }
            }
        }

        $venue->total_capacity = $totalSeats;
        $venue->save();

        SugarApplication::redirect('index.php?module=ev_Venues&action=DetailView&record=' . urlencode($venue->id));
    }

    protected function rowIndexToLabel(string $startLabel, int $offset): string
    {
        $num = 0;
        $len = strlen($startLabel);
        for ($i = 0; $i < $len; $i++) {
            $num = $num * 26 + (ord($startLabel[$i]) - 64);
        }
        $num += $offset;

        $out = '';
        while ($num > 0) {
            $rem = ($num - 1) % 26;
            $out = chr(65 + $rem) . $out;
            $num = intdiv($num - 1, 26);
        }
        return $out;
    }
}