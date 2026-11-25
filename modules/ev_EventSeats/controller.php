<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

class ev_EventSeatsController extends SugarController
{
    public function action_SeatPicker()
    {
        $this->view = 'seatpicker';
    }
}
