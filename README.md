# ContactInfo History Extension for CiviCRM

Track historical changes to contact addresses, emails, and phone numbers using database triggers. View a complete change timeline for any contact.

## Features

- **Automatic History Tracking**: Database triggers capture changes when addresses, emails, or phone numbers are added, updated, or deleted
- **Significant Change Detection**: Only creates new history entries for meaningful changes (e.g., address or email changes), while silently updating non-significant fields (e.g., geocode updates)
- **Contact Merge Support**: When contacts are merged, history records are reassigned to the winning contact while preserving the original contact ID for audit purposes
- **Contact History Tab**: Adds a tab to the contact view showing complete history for addresses, emails, and phones
- **API4 Support**: Full API4 entities for integration with other systems
- **Permission-Based Management**: History management restricted to users with appropriate permissions

## Requirements

- CiviCRM 6.9 or higher
- PHP 8.3 or higher
- MySQL/MariaDB with trigger support
- Smarty 2 or Smarty 5

## Installation

1. Download the extension to your CiviCRM extensions directory
2. **Complete any data deduplication work first** — the initial history snapshot should capture clean data
3. Enable the extension through Administer > System Settings > Extensions
4. History tables will be created and populated with current data automatically

## Usage

### Viewing History

1. Navigate to any contact record
2. Click the "Contact History" tab
3. View address, email, and phone history with start/end dates
4. Records with no end date are current; ended records are shown with a gray background

### Understanding Merged Records

When contacts are merged, the history tab shows the complete trail:
- Records where `orig_contact_id` differs from `contact_id` came from a merged contact
- The original contact ID is preserved for audit purposes

### API4 Access

The extension provides three API4 entities:

- `ContactinfoHistoryAddress`
- `ContactinfoHistoryEmail`
- `ContactinfoHistoryPhone`

Example usage:
```php
$history = \Civi\Api4\ContactinfoHistoryAddress::get()
  ->addWhere('contact_id', '=', 123)
  ->addOrderBy('start_date', 'DESC')
  ->execute();
```

## Permissions

- **View Contact History**: Included with standard CiviCRM contact view permissions
- **Manage ContactInfo History**: Required to edit/delete history records (future feature, defaults to admin only)

## Database Structure

The extension creates three history tables by copying the structure of the CiviCRM source tables:

- `civicrm_contactinfo_history_address` (from `civicrm_address`)
- `civicrm_contactinfo_history_email` (from `civicrm_email`)
- `civicrm_contactinfo_history_phone` (from `civicrm_phone`)

Each history table includes all columns from the source table, plus:

| Column | Description |
|--------|-------------|
| `id` | Auto-increment primary key for the history record |
| `original_id` | The original record ID from the source table |
| `orig_contact_id` | The original contact ID (preserved through merges) |
| `start_date` | When this version of the record was created |
| `modified_date` | When this history record was last updated |
| `end_date` | When this record was superseded or deleted (NULL = current) |

## Technical Notes

- Triggers are managed via CiviCRM's `hook_civicrm_triggerInfo` for reliability
- History tables are created using `CREATE TABLE ... LIKE` to match the source table schema exactly
- Table charset is `utf8mb4_unicode_ci` for full Unicode support
- Contact merges are handled via `hook_civicrm_merge` to preserve audit trail

## Troubleshooting

### Common Issues

1. **Triggers not working**: Ensure your database user has CREATE TRIGGER privileges. Clear CiviCRM caches to rebuild triggers.
2. **Missing history tab**: Clear CiviCRM caches (`cv flush`) and check that the extension is enabled.
3. **Large initial population**: On databases with many contacts, the initial data population on enable may take some time. This is normal.

### Debugging

- Check CiviCRM logs for SQL errors during installation
- Verify triggers exist: `SHOW TRIGGERS LIKE 'civicrm_address'`
- Confirm history tables contain expected data via API4 or direct SQL

## Support

Report issues at: https://github.com/kenmoellman/civi_contactinfo_history/issues

## License

AGPL-3.0
