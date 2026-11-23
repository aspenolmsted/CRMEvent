<?php

$manifest = [
    'acceptable_sugar_versions' => [
        'regex_matches' => ['7\.14\..*'],
    ],
    'acceptable_sugar_flavors' => ['CE'],
    'readme' => '',
    'key' => 'ev',
    'author' => 'Aspen Olmsted',
    'description' => 'Event Ticketing and Registration extension for SuiteCRM (extends FP_events with ev_TicketTypes and ev_Registrations).',
    'icon' => '',
    'is_uninstallable' => true,
    'name' => 'CRMEvent',
    'published_date' => '2026-11-23 00:00:00',
    'type' => 'module',
    'version' => '1.0.0',
];

$installdefs = [
    'id' => 'CRMEvent_v1_0_0',
    'beans' => [
        [
            'module' => 'ev_TicketTypes',
            'class' => 'ev_TicketTypes',
            'path' => 'modules/ev_TicketTypes/ev_TicketTypes.php',
            'tab' => true,
        ],
        [
            'module' => 'ev_Registrations',
            'class' => 'ev_Registrations',
            'path' => 'modules/ev_Registrations/ev_Registrations.php',
            'tab' => true,
        ],
    ],
    'copy' => [
        [
            'from' => '<basepath>/modules/ev_TicketTypes',
            'to' => 'modules/ev_TicketTypes',
        ],
        [
            'from' => '<basepath>/modules/ev_Registrations',
            'to' => 'modules/ev_Registrations',
        ],
        [
            'from' => '<basepath>/custom/Extension/modules/FP_events/Ext/Vardefs/ev_event_registration_fields.php',
            'to' => 'custom/Extension/modules/FP_events/Ext/Vardefs/ev_event_registration_fields.php',
        ],
        [
            'from' => '<basepath>/custom/Extension/modules/FP_events/Ext/Layoutdefs/ev_event_subpanels.php',
            'to' => 'custom/Extension/modules/FP_events/Ext/Layoutdefs/ev_event_subpanels.php',
        ],
        [
            'from' => '<basepath>/custom/Extension/modules/Contacts/Ext/Vardefs/ev_contact_registrations_rel.php',
            'to' => 'custom/Extension/modules/Contacts/Ext/Vardefs/ev_contact_registrations_rel.php',
        ],
        [
            'from' => '<basepath>/custom/Extension/modules/Contacts/Ext/Layoutdefs/ev_contact_subpanels.php',
            'to' => 'custom/Extension/modules/Contacts/Ext/Layoutdefs/ev_contact_subpanels.php',
        ],
    ],
    'language' => [
        [
            'from' => '<basepath>/custom/Extension/application/Ext/Language/en_us.ev_events.php',
            'to_module' => 'application',
            'language' => 'en_us',
        ],
        [
            'from' => '<basepath>/custom/Extension/modules/FP_events/Ext/Language/en_us.ev_events.php',
            'to_module' => 'FP_events',
            'language' => 'en_us',
        ],
        [
            'from' => '<basepath>/custom/Extension/modules/Contacts/Ext/Language/en_us.ev_events.php',
            'to_module' => 'Contacts',
            'language' => 'en_us',
        ],
    ],
    'logic_hooks' => [
        [
            'module' => 'ev_Registrations',
            'hook' => 'before_save',
            'order' => 10,
            'description' => 'Generate check-in token and confirmation reference on registration save',
            'file' => 'modules/ev_Registrations/ev_RegistrationsHooks.php',
            'class' => 'ev_RegistrationsHooks',
            'function' => 'beforeSaveRegistration',
        ],
        [
            'module' => 'ev_Registrations',
            'hook' => 'after_save',
            'order' => 10,
            'description' => 'Roll up ticket quantities sold and event remaining seats, and sync native FP_events attendee link',
            'file' => 'modules/ev_Registrations/ev_RegistrationsHooks.php',
            'class' => 'ev_RegistrationsHooks',
            'function' => 'afterSaveRegistration',
        ],
        [
            'module' => 'ev_Registrations',
            'hook' => 'after_delete',
            'order' => 10,
            'description' => 'Recalculate ticket quantities sold and event remaining seats after registration deletion',
            'file' => 'modules/ev_Registrations/ev_RegistrationsHooks.php',
            'class' => 'ev_RegistrationsHooks',
            'function' => 'afterDeleteRegistration',
        ],
    ],
];
