# ContactInfo History Extension - Design Document

## Overview

CiviCRM extension to track historical changes to contact addresses, emails, and phone numbers using database triggers. Provides a contact tab for viewing change history and handles contact merge scenarios.

## Naming Convention

- **Extension key**: `com.moellman.contactinfo_history`
- **Short name / file prefix**: `contactinfo_history`
- **Class prefix**: `CRM_ContactinfoHistory`
- **Table prefix**: `civicrm_contactinfo_history_`
- **API4 entities**: `ContactinfoHistoryAddress`, `ContactinfoHistoryEmail`, `ContactinfoHistoryPhone`
- **Menu paths**: `civicrm/contact/view/contactinfohistory`, `civicrm/contactinfohistory/edit`, `civicrm/contactinfohistory/delete`
- **Permission**: `manage contactinfo history`

## Requirements

- CiviCRM 6.9+
- PHP 8.3+
- MySQL/MariaDB with trigger support
- Smarty 2 or Smarty 5 compatible

## Database Design

### History Tables

Three history tables are created dynamically by copying the structure of the CiviCRM source tables:

- `civicrm_contactinfo_history_address` (from `civicrm_address`)
- `civicrm_contactinfo_history_email` (from `civicrm_email`)
- `civicrm_contactinfo_history_phone` (from `civicrm_phone`)

#### Table Creation Process (Upgrader::install)

1. `CREATE TABLE ... LIKE civicrm_address` — copies exact column structure and indexes
2. `ALTER TABLE` — removes auto_increment from `id`, drops primary key
3. `CHANGE COLUMN id original_id` — renames original ID column
4. Add new columns:
   - `id` — new auto_increment primary key
   - `orig_contact_id` — preserves original contact_id before any merges
   - `start_date` — when this history record was created (DEFAULT CURRENT_TIMESTAMP)
   - `modified_date` — when this history record was last modified (ON UPDATE CURRENT_TIMESTAMP)
   - `end_date` — when this record was superseded/deleted (NULL = current)
5. Add indexes on `original_id`, `orig_contact_id`
6. Convert to `utf8mb4_unicode_ci` charset

#### Column: orig_contact_id

- On initial creation: set to same as `contact_id`
- On contact merge: preserves the loser's contact_id, while `contact_id` is updated to the winner
- Allows viewing which records came from merged contacts

### Triggers (via hook_civicrm_triggerInfo)

CiviCRM's native trigger management system is used. Triggers are registered via `hook_civicrm_triggerInfo` and CiviCRM handles DELIMITER issues, trigger rebuilding on cache clear, and coexistence with other extensions' triggers.

#### AFTER INSERT

Copies new record to history table with `orig_contact_id = contact_id`.

#### AFTER UPDATE

Detects "significant changes" using NULL-safe comparison (`<=>`):

**Address significant fields**: contact_id, location_type_id, is_primary, is_billing, street_address, supplemental_address_1, supplemental_address_2, supplemental_address_3, city, county_id, state_province_id, postal_code, postal_code_suffix, country_id

**Email significant fields**: contact_id, location_type_id, email, is_primary, is_billing, on_hold

**Phone significant fields**: contact_id, location_type_id, is_primary, is_billing, phone, phone_ext, phone_type_id

If significant change detected:
1. Close current history record (set `end_date = NOW()`)
2. Insert new history record

If only non-significant change (e.g., geocode update):
1. Update the existing history record in place (keeps latest values without creating new entry)

#### AFTER DELETE

Sets `end_date = NOW()` on the active history record.

### Contact Merge Handling (via hook_civicrm_merge)

When contacts are merged (loser → winner):

1. **Preserve original ownership**: Set `orig_contact_id = contact_id` on loser's history records (only if not already set by a prior merge)
2. **Close active records**: Set `end_date = NOW()` on loser's active history records
3. **Reassign to winner**: Update `contact_id = winner` on all loser's history records

This runs before CiviCRM's own merge SQL. When CiviCRM then updates the source tables (civicrm_address, etc.), our triggers fire and create new history records showing the address now belongs to the winner.

**Result**: History shows a complete trail — records with `orig_contact_id != contact_id` came from a merged contact.

## Hooks Implemented

| Hook | Purpose |
|------|---------|
| `hook_civicrm_config` | Register template directory and include path |
| `hook_civicrm_install` | Create history tables (via Upgrader) |
| `hook_civicrm_uninstall` | Drop history tables (via Upgrader) |
| `hook_civicrm_enable` | Populate initial data, rebuild triggers (via Upgrader) |
| `hook_civicrm_disable` | Rebuild triggers to remove ours (via Upgrader) |
| `hook_civicrm_entityTypes` | Register DAO entities |
| `hook_civicrm_tabset` | Add "Contact History" tab to contact view |
| `hook_civicrm_permission` | Define "manage contactinfo history" permission |
| `hook_civicrm_triggerInfo` | Register database triggers for history tracking |
| `hook_civicrm_merge` | Handle contact merge — reassign history records |

## File Structure

```
contactinfo_history/
├── contactinfo_history.php          # Main hook file
├── contactinfo_history.civix.php    # Civix utilities
├── info.xml                         # Extension metadata
├── DESIGN.md                        # This file
├── README.md                        # User documentation
├── LICENSE                          # AGPL-3.0
├── CRM/ContactinfoHistory/
│   ├── Upgrader.php                 # Install/enable/disable/uninstall logic
│   ├── BAO/
│   │   ├── ContactinfoHistoryAddress.php
│   │   ├── ContactinfoHistoryEmail.php
│   │   └── ContactinfoHistoryPhone.php
│   ├── DAO/
│   │   ├── ContactinfoHistoryAddress.php
│   │   ├── ContactinfoHistoryEmail.php
│   │   └── ContactinfoHistoryPhone.php
│   └── Page/
│       └── ContactinfoHistory.php   # Contact history tab controller
├── Civi/Api4/
│   ├── ContactinfoHistoryAddress.php
│   ├── ContactinfoHistoryEmail.php
│   └── ContactinfoHistoryPhone.php
├── sql/
│   └── uninstall.sql                # Drop history tables
├── templates/CRM/ContactinfoHistory/
│   └── Page/
│       └── ContactinfoHistory.tpl   # Contact history tab template
└── xml/Menu/
    └── contactinfo_history.xml      # Menu/route definitions
```

## Initial Data Population (on enable)

When the extension is enabled for the first time (history tables are empty), all existing records from civicrm_address, civicrm_email, and civicrm_phone are copied into the history tables. Column lists are built dynamically from INFORMATION_SCHEMA to ensure all source columns are captured.

**Important**: Enable the extension AFTER completing any data deduplication work, so the initial snapshot captures clean data.

## Future Enhancements

- Edit/Delete forms for history records (permission-gated)
- SearchKit integration for querying history data
- Export/reporting capabilities
- Configurable significant-change field lists

## Version

- **1.0.0** — Initial release with full history tracking, merge support, contact tab
