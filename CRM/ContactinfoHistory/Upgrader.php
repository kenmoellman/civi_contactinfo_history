<?php

use CRM_ContactinfoHistory_ExtensionUtil as E;

/**
 * Collection of upgrade steps for the ContactInfo History extension.
 */
class CRM_ContactinfoHistory_Upgrader extends CRM_Extension_Upgrader_Base {

  /**
   * Map of source tables to history tables.
   */
  private const HISTORY_TABLES = [
    'civicrm_address' => 'civicrm_contactinfo_history_address',
    'civicrm_email' => 'civicrm_contactinfo_history_email',
    'civicrm_phone' => 'civicrm_contactinfo_history_phone',
  ];

  /**
   * @var CRM_ContactinfoHistory_Upgrader
   */
  private static $_instance = NULL;

  /**
   * Get singleton instance.
   */
  public static function instance(): self {
    if (!self::$_instance) {
      self::$_instance = new self();
      self::$_instance->extensionName = E::LONG_NAME;
      self::$_instance->extensionDir = E::path();
    }
    return self::$_instance;
  }

  /**
   * Install: create history tables by copying source table structures.
   */
  public function install(): void {
    foreach (self::HISTORY_TABLES as $sourceTable => $historyTable) {
      $this->createHistoryTable($sourceTable, $historyTable);
    }
  }

  /**
   * Enable: populate history tables with existing data and rebuild triggers.
   */
  public function enable(): void {
    foreach (self::HISTORY_TABLES as $sourceTable => $historyTable) {
      $this->populateHistoryTable($sourceTable, $historyTable);
    }
    CRM_Core_DAO::triggerRebuild();
  }

  /**
   * Disable: rebuild triggers (removes ours since extension is disabled).
   */
  public function disable(): void {
    CRM_Core_DAO::triggerRebuild();
  }

  /**
   * Uninstall: drop history tables.
   */
  public function uninstall(): void {
    foreach (self::HISTORY_TABLES as $historyTable) {
      CRM_Core_DAO::executeQuery("DROP TABLE IF EXISTS `{$historyTable}`");
    }
    CRM_Core_DAO::triggerRebuild();
  }

  /**
   * Create a history table by copying the source table structure,
   * then adding our custom columns.
   */
  private function createHistoryTable(string $sourceTable, string $historyTable): void {
    // Check if table already exists
    $exists = CRM_Core_DAO::singleValueQuery(
      "SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = %1",
      [1 => [$historyTable, 'String']]
    );
    if ($exists) {
      return;
    }

    // Copy table structure (no data, no foreign keys)
    CRM_Core_DAO::executeQuery("CREATE TABLE `{$historyTable}` LIKE `{$sourceTable}`");

    // Remove auto_increment from the original id column
    CRM_Core_DAO::executeQuery("ALTER TABLE `{$historyTable}` MODIFY COLUMN `id` INT UNSIGNED NOT NULL");

    // Drop primary key so we can rename the column
    CRM_Core_DAO::executeQuery("ALTER TABLE `{$historyTable}` DROP PRIMARY KEY");

    // Rename id -> original_id
    CRM_Core_DAO::executeQuery("ALTER TABLE `{$historyTable}` CHANGE COLUMN `id` `original_id` INT UNSIGNED");

    // Add our custom columns and new primary key
    CRM_Core_DAO::executeQuery("ALTER TABLE `{$historyTable}`
      ADD COLUMN `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
      ADD COLUMN `orig_contact_id` INT UNSIGNED AFTER `contact_id`,
      ADD COLUMN `start_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      ADD COLUMN `modified_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      ADD COLUMN `end_date` TIMESTAMP NULL DEFAULT NULL,
      ADD INDEX `idx_original_id` (`original_id`),
      ADD INDEX `idx_orig_contact_id` (`orig_contact_id`),
      CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
    ");

    // Drop any foreign key constraints that were copied
    $fkDao = CRM_Core_DAO::executeQuery(
      "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
       WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = %1 AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
      [1 => [$historyTable, 'String']]
    );
    while ($fkDao->fetch()) {
      CRM_Core_DAO::executeQuery("ALTER TABLE `{$historyTable}` DROP FOREIGN KEY `{$fkDao->CONSTRAINT_NAME}`");
    }
  }

  /**
   * Populate a history table with existing data from the source table.
   * Only runs if the history table is empty.
   */
  private function populateHistoryTable(string $sourceTable, string $historyTable): void {
    $count = CRM_Core_DAO::singleValueQuery("SELECT COUNT(*) FROM `{$historyTable}`");
    if ($count > 0) {
      return;
    }

    // Get columns from source table (excluding 'id' which maps to 'original_id')
    $sourceColumns = $this->getTableColumns($sourceTable);
    $sourceColumns = array_diff($sourceColumns, ['id']);

    // Get columns from history table (excluding our custom columns)
    $excludeColumns = ['id', 'original_id', 'orig_contact_id', 'start_date', 'modified_date', 'end_date'];
    $historyColumns = $this->getTableColumns($historyTable);
    $historyColumns = array_diff($historyColumns, $excludeColumns);

    // Find common columns
    $commonColumns = array_values(array_intersect($sourceColumns, $historyColumns));
    if (empty($commonColumns)) {
      return;
    }

    $colList = implode(', ', array_map(fn($c) => "`{$c}`", $commonColumns));

    $sql = "INSERT INTO `{$historyTable}` (`original_id`, `orig_contact_id`, {$colList})
            SELECT `id`, `contact_id`, {$colList} FROM `{$sourceTable}`";
    CRM_Core_DAO::executeQuery($sql);
  }

  /**
   * Get column names for a table.
   */
  private function getTableColumns(string $tableName): array {
    $columns = [];
    $dao = CRM_Core_DAO::executeQuery(
      "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
       WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = %1
       ORDER BY ORDINAL_POSITION",
      [1 => [$tableName, 'String']]
    );
    while ($dao->fetch()) {
      $columns[] = $dao->COLUMN_NAME;
    }
    return $columns;
  }

}
