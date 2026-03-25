<?php

use CRM_ContactinfoHistory_ExtensionUtil as E;

class CRM_ContactinfoHistory_BAO_ContactinfoHistoryEmail extends CRM_ContactinfoHistory_DAO_ContactinfoHistoryEmail {

  /**
   * Get history record count for a contact.
   */
  public static function getHistoryCount(int $contactId): int {
    $sql = "SELECT COUNT(*) FROM civicrm_contactinfo_history_email WHERE contact_id = %1";
    return (int) CRM_Core_DAO::singleValueQuery($sql, [1 => [$contactId, 'Integer']]);
  }

  /**
   * Get history records for a contact with resolved location type name.
   */
  public static function getHistory(int $contactId): array {
    $sql = "
      SELECT h.*,
             lt.display_name AS location_type
      FROM civicrm_contactinfo_history_email h
      LEFT JOIN civicrm_location_type lt ON h.location_type_id = lt.id
      WHERE h.contact_id = %1
      ORDER BY h.start_date DESC, h.id DESC
    ";
    $dao = CRM_Core_DAO::executeQuery($sql, [1 => [$contactId, 'Integer']]);
    $results = [];
    while ($dao->fetch()) {
      $results[] = $dao->toArray();
    }
    return $results;
  }

}
