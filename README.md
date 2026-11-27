# CRMEvent — Event Ticketing, Registration & Interactive Venue Seating for SuiteCRM & Joomla

**Version:** 2.0.18  
**Author:** Aspen Olmsted ([alliance.biz](https://alliance.biz))  
**Compatibility:** SuiteCRM 7.x / 8.x (SugarCRM 6.5 – 8.x CE) & Joomla 4.x / 5.x  

## Overview

**CRMEvent** is an end-to-end event ticketing, registration, and interactive SVG venue seating solution combining a SuiteCRM module package with a native Joomla 4/5 site component (`com_crmevent`). SuiteCRM manages venues, floor-plan layouts, seat templates, ticket tiers, and registrations, while Joomla provides the public event listing, interactive seat-selection map, and multi-seat registration checkout.

---

## Repository Structure

```text
CRMEvent/
├── manifest.php                                # SuiteCRM Module Loader manifest (v2.0.18)
├── README.md
├── modules/                                    # SuiteCRM custom beans, hooks & public JSON endpoint
│   ├── ev_EventSeats/
│   │   ├── ev_EventSeats.php
│   │   └── publicmap.php                       # Unauthenticated JSON endpoint for Joomla seat maps
│   ├── ev_Registrations/
│   │   ├── ev_Registrations.php
│   │   └── ev_RegistrationsHooks.php           # Confirmation codes, QR tokens & inventory rollups
│   ├── ev_TicketTypes/
│   │   └── ev_TicketTypes.php
│   ├── ev_Venues/
│   │   ├── ev_Venues.php
│   │   └── ev_EventVenueHooks.php              # Clones venue seat templates onto events
│   └── ev_VenueSeats/
│       └── ev_VenueSeats.php
├── custom/                                     # SuiteCRM extensions (EntryPointRegistry, Vardefs, Subpanels)
│   └── Extension/
│       ├── application/
│       │   └── Ext/
│       │       ├── EntryPointRegistry/
│       │       │   └── crmevent_publicmap.php
│       │       └── Language/
│       │           └── en_us.ev_events.php
│       └── modules/
│           ├── Contacts/
│           │   └── Ext/
│           │       ├── Language/en_us.ev_events.php
│           │       ├── Layoutdefs/ev_contact_subpanels.php
│           │       └── Vardefs/ev_contact_registrations_rel.php
│           └── FP_events/
│               └── Ext/
│                   ├── Language/en_us.ev_events.php
│                   ├── Layoutdefs/ev_event_subpanels.php
│                   └── Vardefs/ev_event_registration_fields.php
└── joomla/com_crmevent/                        # Joomla 4/5 Site & Admin Component
    ├── com_crmevent.xml
    ├── services/
    │   └── provider.php
    └── site/
        ├── src/
        │   ├── Controller/
        │   ├── Helper/
        │   │   └── SuiteCrmClient.php          # Publicmap fetcher + REST v4.1 registration client
        │   ├── Model/
        │   └── View/
        └── tmpl/                               # Event list, SVG seat map & confirmation views