<?php

use CRM_ContactinfoHistory_ExtensionUtil as E;

class CRM_ContactinfoHistory_BAO_ContactinfoHistoryPhone extends CRM_ContactinfoHistory_DAO_ContactinfoHistoryPhone {

  /**
   * Get history record count for a contact.
   */
  public static function getHistoryCount(int $contactId): int {
    $sql = "SELECT COUNT(*) FROM civicrm_contactinfo_history_phone WHERE contact_id = %1";
    return (int) CRM_Core_DAO::singleValueQuery($sql, [1 => [$contactId, 'Integer']]);
  }

  /**
   * Get history records for a contact with resolved location type and phone type names.
   */
  public static function getHistory(int $contactId): array {
    $sql = "
      SELECT h.*,
             lt.display_name AS location_type,
             ov.label AS phone_type
      FROM civicrm_contactinfo_history_phone h
      LEFT JOIN civicrm_location_type lt ON h.location_type_id = lt.id
      LEFT JOIN civicrm_option_value ov ON h.phone_type_id = ov.value
        AND ov.option_group_id = (SELECT id FROM civicrm_option_group WHERE name = 'phone_type')
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
