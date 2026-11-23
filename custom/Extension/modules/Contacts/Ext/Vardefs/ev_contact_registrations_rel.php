<?php

$dictionary['Contact']['fields']['ev_registrations'] = [
    'name' => 'ev_registrations',
    'type' => 'link',
    'relationship' => 'contacts_ev_registrations',
    'source' => 'non-db',
    'vname' => 'LBL_EV_REGISTRATIONS',
];

$dictionary['Contact']['fields']['ev_purchased_registrations'] = [
    'name' => 'ev_purchased_registrations',
    'type' => 'link',
    'relationship' => 'purchaser_contacts_ev_registrations',
    'source' => 'non-db',
    'vname' => 'LBL_EV_PURCHASED_REGISTRATIONS',
];
