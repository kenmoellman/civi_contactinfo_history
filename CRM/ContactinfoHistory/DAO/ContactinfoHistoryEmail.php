<?php

use CRM_ContactinfoHistory_ExtensionUtil as E;

/**
 * DAO for civicrm_contactinfo_history_email table.
 */
class CRM_ContactinfoHistory_DAO_ContactinfoHistoryEmail extends CRM_Core_DAO {

  const EXT = E::LONG_NAME;

  public static $_tableName = 'civicrm_contactinfo_history_email';
  public static $_log = FALSE;

  public $id;
  public $original_id;
  public $contact_id;
  public $orig_contact_id;
  public $start_date;
  public $modified_date;
  public $end_date;

  public static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'id' => [
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('ID'),
          'required' => TRUE,
          'where' => 'civicrm_contactinfo_history_email.id',
          'table_name' => 'civicrm_contactinfo_history_email',
          'entity' => 'ContactinfoHistoryEmail',
          'bao' => 'CRM_ContactinfoHistory_BAO_ContactinfoHistoryEmail',
          'localizable' => 0,
        ],
        'original_id' => [
          'name' => 'original_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Original Email ID'),
          'where' => 'civicrm_contactinfo_history_email.original_id',
          'table_name' => 'civicrm_contactinfo_history_email',
          'entity' => 'ContactinfoHistoryEmail',
          'bao' => 'CRM_ContactinfoHistory_BAO_ContactinfoHistoryEmail',
          'localizable' => 0,
        ],
        'contact_id' => [
          'name' => 'contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Contact ID'),
          'where' => 'civicrm_contactinfo_history_email.contact_id',
          'table_name' => 'civicrm_contactinfo_history_email',
          'entity' => 'ContactinfoHistoryEmail',
          'bao' => 'CRM_ContactinfoHistory_BAO_ContactinfoHistoryEmail',
          'localizable' => 0,
          'FKClassName' => 'CRM_Contact_DAO_Contact',
        ],
        'orig_contact_id' => [
          'name' => 'orig_contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Original Contact ID'),
          'description' => E::ts('Contact ID before any merges'),
          'where' => 'civicrm_contactinfo_history_email.orig_contact_id',
          'table_name' => 'civicrm_contactinfo_history_email',
          'entity' => 'ContactinfoHistoryEmail',
          'bao' => 'CRM_ContactinfoHistory_BAO_ContactinfoHistoryEmail',
          'localizable' => 0,
          'FKClassName' => 'CRM_Contact_DAO_Contact',
        ],
        'start_date' => [
          'name' => 'start_date',
          'type' => CRM_Utils_Type::T_TIMESTAMP,
          'title' => E::ts('Start Date'),
          'where' => 'civicrm_contactinfo_history_email.start_date',
          'table_name' => 'civicrm_contactinfo_history_email',
          'entity' => 'ContactinfoHistoryEmail',
          'bao' => 'CRM_ContactinfoHistory_BAO_ContactinfoHistoryEmail',
          'localizable' => 0,
        ],
        'modified_date' => [
          'name' => 'modified_date',
          'type' => CRM_Utils_Type::T_TIMESTAMP,
          'title' => E::ts('Modified Date'),
          'where' => 'civicrm_contactinfo_history_email.modified_date',
          'table_name' => 'civicrm_contactinfo_history_email',
          'entity' => 'ContactinfoHistoryEmail',
          'bao' => 'CRM_ContactinfoHistory_BAO_ContactinfoHistoryEmail',
          'localizable' => 0,
        ],
        'end_date' => [
          'name' => 'end_date',
          'type' => CRM_Utils_Type::T_TIMESTAMP,
          'title' => E::ts('End Date'),
          'where' => 'civicrm_contactinfo_history_email.end_date',
          'table_name' => 'civicrm_contactinfo_history_email',
          'entity' => 'ContactinfoHistoryEmail',
          'bao' => 'CRM_ContactinfoHistory_BAO_ContactinfoHistoryEmail',
          'localizable' => 0,
        ],
      ];
    }
    return Civi::$statics[__CLASS__]['fields'];
  }

  public static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }

  public static function getTableName() {
    return self::$_tableName;
  }

}
