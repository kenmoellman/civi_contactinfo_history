<?php

require_once 'contactinfo_history.civix.php';

use CRM_ContactinfoHistory_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 */
function contactinfo_history_civicrm_config(&$config): void {
  _contactinfo_history_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 */
function contactinfo_history_civicrm_xmlMenu(&$files) {
  _contactinfo_history_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 */
function contactinfo_history_civicrm_install(): void {
  _contactinfo_history_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 */
function contactinfo_history_civicrm_enable(): void {
  _contactinfo_history_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 */
function contactinfo_history_civicrm_disable(): void {
  _contactinfo_history_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_uninstall().
 */
function contactinfo_history_civicrm_uninstall(): void {
  _contactinfo_history_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_upgrade().
 */
function contactinfo_history_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _contactinfo_history_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_entityTypes().
 */
function contactinfo_history_civicrm_entityTypes(&$entityTypes): void {
  _contactinfo_history_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_tabset().
 *
 * Adds "Contact History" tab to contact view.
 */
function contactinfo_history_civicrm_tabset($tabsetName, &$tabs, $context) {
  if ($tabsetName === 'civicrm/contact/view' && !empty($context['contact_id'])) {
    $contactId = $context['contact_id'];
    $count = CRM_ContactinfoHistory_BAO_ContactinfoHistoryAddress::getHistoryCount($contactId)
           + CRM_ContactinfoHistory_BAO_ContactinfoHistoryEmail::getHistoryCount($contactId)
           + CRM_ContactinfoHistory_BAO_ContactinfoHistoryPhone::getHistoryCount($contactId);

    $tabs[] = [
      'id' => 'contactinfohistory',
      'url' => CRM_Utils_System::url('civicrm/contact/view/contactinfohistory', [
        'cid' => $contactId,
        'snippet' => 4,
      ]),
      'title' => E::ts('Contact History'),
      'weight' => 300,
      'count' => $count,
    ];
  }
}

/**
 * Implements hook_civicrm_permission().
 */
function contactinfo_history_civicrm_permission(&$permissions) {
  $permissions['manage contactinfo history'] = [
    'label' => E::ts('CiviCRM: Manage ContactInfo History'),
    'description' => E::ts('Edit and delete contact info history records'),
  ];
}

/**
 * Implements hook_civicrm_triggerInfo().
 *
 * Registers database triggers for tracking changes to addresses, emails, and phones.
 */
function contactinfo_history_civicrm_triggerInfo(&$info, $tableName = NULL) {
  $tableMap = [
    'civicrm_address' => [
      'history' => 'civicrm_contactinfo_history_address',
      'significant' => [
        'contact_id', 'location_type_id', 'is_primary', 'is_billing',
        'street_address', 'supplemental_address_1', 'supplemental_address_2',
        'supplemental_address_3', 'city', 'county_id', 'state_province_id',
        'postal_code', 'postal_code_suffix', 'country_id',
      ],
    ],
    'civicrm_email' => [
      'history' => 'civicrm_contactinfo_history_email',
      'significant' => [
        'contact_id', 'location_type_id', 'email', 'is_primary', 'is_billing', 'on_hold',
      ],
    ],
    'civicrm_phone' => [
      'history' => 'civicrm_contactinfo_history_phone',
      'significant' => [
        'contact_id', 'location_type_id', 'is_primary', 'is_billing',
        'phone', 'phone_ext', 'phone_type_id',
      ],
    ],
  ];

  foreach ($tableMap as $sourceTable => $config) {
    if ($tableName !== NULL && $tableName !== $sourceTable) {
      continue;
    }

    $historyTable = $config['history'];

    // Check that history table exists (may not during install)
    $tableExists = CRM_Core_DAO::singleValueQuery(
      "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = %1",
      [1 => [$historyTable, 'String']]
    );
    if (!$tableExists) {
      continue;
    }

    // Get columns common to both source and history tables (excluding our custom columns)
    $columns = _contactinfo_history_get_common_columns($sourceTable, $historyTable);
    if (empty($columns)) {
      continue;
    }

    $colList = implode(', ', $columns);
    $newColList = implode(', ', array_map(fn($c) => "NEW.{$c}", $columns));
    $updateSetList = implode(', ', array_map(fn($c) => "{$c} = NEW.{$c}", $columns));

    // Build significant change condition
    $sigConditions = [];
    foreach ($config['significant'] as $field) {
      if (in_array($field, $columns)) {
        $sigConditions[] = "NOT (OLD.{$field} <=> NEW.{$field})";
      }
    }
    $sigCheck = implode(' OR ', $sigConditions);

    // AFTER INSERT trigger
    $info[] = [
      'table' => [$sourceTable],
      'when' => 'AFTER',
      'event' => ['INSERT'],
      'sql' => "\nINSERT INTO {$historyTable} (original_id, orig_contact_id, {$colList}) VALUES (NEW.id, NEW.contact_id, {$newColList});\n",
    ];

    // AFTER UPDATE trigger
    $updateSql = "\nIF ({$sigCheck}) THEN\n"
      . "  UPDATE {$historyTable} SET end_date = NOW() WHERE original_id = OLD.id AND end_date IS NULL;\n"
      . "  INSERT INTO {$historyTable} (original_id, orig_contact_id, {$colList}) VALUES (NEW.id, NEW.contact_id, {$newColList});\n"
      . "ELSE\n"
      . "  UPDATE {$historyTable} SET {$updateSetList} WHERE original_id = OLD.id AND end_date IS NULL;\n"
      . "END IF;\n";

    $info[] = [
      'table' => [$sourceTable],
      'when' => 'AFTER',
      'event' => ['UPDATE'],
      'sql' => $updateSql,
    ];

    // AFTER DELETE trigger
    $info[] = [
      'table' => [$sourceTable],
      'when' => 'AFTER',
      'event' => ['DELETE'],
      'sql' => "\nUPDATE {$historyTable} SET end_date = NOW() WHERE original_id = OLD.id AND end_date IS NULL;\n",
    ];
  }
}

/**
 * Get columns that exist in both source and history tables, excluding our custom columns.
 *
 * @param string $sourceTable
 * @param string $historyTable
 * @return array
 */
function _contactinfo_history_get_common_columns(string $sourceTable, string $historyTable): array {
  $excludeColumns = ['id', 'original_id', 'orig_contact_id', 'start_date', 'modified_date', 'end_date'];

  $getColumns = function ($table) {
    $columns = [];
    $dao = CRM_Core_DAO::executeQuery(
      "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = %1 ORDER BY ORDINAL_POSITION",
      [1 => [$table, 'String']]
    );
    while ($dao->fetch()) {
      $columns[] = $dao->COLUMN_NAME;
    }
    return $columns;
  };

  $sourceColumns = array_diff($getColumns($sourceTable), ['id']);
  $historyColumns = array_diff($getColumns($historyTable), $excludeColumns);

  return array_values(array_intersect($sourceColumns, $historyColumns));
}

/**
 * Implements hook_civicrm_merge().
 *
 * When contacts are merged, reassign history records from the loser to the winner
 * while preserving the original contact ID in orig_contact_id.
 */
function contactinfo_history_civicrm_merge($type, &$data, $mainId = NULL, $otherId = NULL, $tables = NULL) {
  if ($type === 'sqls' && $mainId && $otherId) {
    $historyTables = [
      'civicrm_contactinfo_history_address',
      'civicrm_contactinfo_history_email',
      'civicrm_contactinfo_history_phone',
    ];

    $mainId = (int) $mainId;
    $otherId = (int) $otherId;

    foreach ($historyTables as $table) {
      // Preserve orig_contact_id (only if not already set by a prior merge)
      array_unshift($data, "UPDATE {$table} SET orig_contact_id = contact_id WHERE contact_id = {$otherId} AND orig_contact_id = contact_id");
      // Close active records for the loser
      array_unshift($data, "UPDATE {$table} SET end_date = NOW() WHERE contact_id = {$otherId} AND end_date IS NULL");
      // Reassign all loser's history to winner
      array_unshift($data, "UPDATE {$table} SET contact_id = {$mainId} WHERE contact_id = {$otherId}");
    }
  }
}
