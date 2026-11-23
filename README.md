# SuiteCRM Event Registration Extension

A lightweight, native-first event registration and attendee management extension for **SuiteCRM 8.x** (and backward-compatible with **SuiteCRM 7.x**). Rather than duplicating core CRM event or contact tables, this extension extends standard CRM modules (**Events (`FP_events`)** and **Contacts**) and introduces two focused ticketing and attendance modules: **Ticket Types (`ev_TicketTypes`)** and **Event Registrations (`ev_Registrations`)**.

---

## Features

* **Hybrid Native + Custom Architecture:** Leverages SuiteCRM's built-in `FP_events` and `Contacts` modules while keeping ticket inventory, pricing tiers, and individual registration ledgers isolated in dedicated tables.
* **Tiered Ticketing & Inventory Control (`ev_TicketTypes`):** Support multiple admission levels per event (e.g., *General Admission*, *VIP Table*, *Early Bird*, *Student/Free*) with per-tier capacity caps, sales windows, and `fair_market_value` tracking for tax-deductible split calculations.
* **Individual Attendee & Group Registration Ledger (`ev_Registrations`):** Track individual seats, group/table purchasers (`purchased_by_contact_id`), badge names, dietary restrictions, and registration statuses (*Pending*, *Confirmed*, *Waitlisted*, *Cancelled*, *Checked In*).
* **Idempotent Payment & QR Check-In Tokens:** Enforce unique payment gateway `transaction_id` indexing to prevent duplicate registrations during webhook retries, and auto-generate unique `checkin_token` UUIDs and `EVT-YYYY-XXXXXX` confirmation codes on creation.
* **Automated Capacity & Sold-Out Rollups:** Logic hooks recalculate `quantity_sold` on `ev_TicketTypes` and `seats_remaining_c` on `FP_events` in real time, automatically flipping ticket tiers to `Sold_Out` when capacity is reached.
* **Native Event Invitee Sync:** Automatically links confirmed and checked-in registrants to the parent `FP_events` native invitee relationship (`fp_events_contacts`) so built-in SuiteCRM event reports and target lists stay synchronized.

---

## Data Model Overview

### Extended Native Modules
| Module | Event Management Role | Key Custom Fields |
| :--- | :--- | :--- |
| **Events (`FP_events`)** | Master Event Record (Venue, Dates, Budget & Capacity) | `capacity_c`, `seats_remaining_c`, `registration_deadline_c`, `is_public_c`, `virtual_meeting_url_c` |
| **Contacts** | Event Attendees & Group/Table Purchasers | Subpanels for **Event Registrations** (attended) and **Purchased Registrations** |

### Custom Modules
| Module | Table | Purpose |
| :--- | :--- | :--- |
| **`ev_TicketTypes`** | `ev_ticket_types` | Ticket pricing tiers, fair market values, sales windows, and seat inventory (`quantity_available`, `quantity_sold`). |
| **`ev_Registrations`** | `ev_registrations` | Individual attendee tickets, confirmation codes, QR check-in tokens (`checkin_token`, `checked_in_at`), and gateway transaction IDs. |

### Entity Relationships
```text
FP_events (1) ──────────< (M) ev_TicketTypes (1) ──────────< (M) ev_Registrations
    │                                                                 │
    └─────────────────────< (M) ev_Registrations (M) >────────────────┤
                                                                      ├── (M:1) Contacts (Attendee)
                                                                      └── (M:1) Contacts (Purchaser)
```

---

## Installation

1. Download or package `CRMEvent.zip` and upload it in SuiteCRM via **Admin → Module Loader**.
2. Click **Install** to deploy the custom modules, language strings, and logic hooks.
3. Navigate to **Admin → Repair → Quick Repair and Rebuild**, scroll to the bottom, and click **Execute** to create the `ev_ticket_types` and `ev_registrations` tables and add the custom columns to `fp_events`.
4. Open **Admin → Studio → Events → Layouts → EditView & DetailView** to place `Total Capacity`, `Seats Remaining`, `Registration Deadline`, `Public Registration Open`, and `Virtual Meeting URL` onto your Event layout, then click **Save & Deploy**.

---

## License

Licensed under the **GNU Affero General Public License v3.0 (AGPL-3.0)**.