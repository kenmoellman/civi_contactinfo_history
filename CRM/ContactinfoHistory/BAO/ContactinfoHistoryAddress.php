<?php

use CRM_ContactinfoHistory_ExtensionUtil as E;

class CRM_ContactinfoHistory_BAO_ContactinfoHistoryAddress extends CRM_ContactinfoHistory_DAO_ContactinfoHistoryAddress {

  /**
   * Get history record count for a contact.
   */
  public static function getHistoryCount(int $contactId): int {
    $sql = "SELECT COUNT(*) FROM civicrm_contactinfo_history_address WHERE contact_id = %1";
    return (int) CRM_Core_DAO::singleValueQuery($sql, [1 => [$contactId, 'Integer']]);
  }

  /**
   * Get history records for a contact with resolved location type, state, and country names.
   */
  public static function getHistory(int $contactId): array {
    $sql = "
      SELECT h.*,
             lt.display_name AS location_type,
             sp.name AS state_province,
             c.name AS country
      FROM civicrm_contactinfo_history_address h
      LEFT JOIN civicrm_location_type lt ON h.location_type_id = lt.id
      LEFT JOIN civicrm_state_province sp ON h.state_province_id = sp.id
      LEFT JOIN civicrm_country c ON h.country_id = c.id
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
