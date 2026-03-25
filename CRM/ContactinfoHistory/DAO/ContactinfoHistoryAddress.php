<?php

use CRM_ContactinfoHistory_ExtensionUtil as E;

/**
 * DAO for civicrm_contactinfo_history_address table.
 */
class CRM_ContactinfoHistory_DAO_ContactinfoHistoryAddress extends CRM_Core_DAO {

  const EXT = E::LONG_NAME;

  /**
   * Static instance to hold the table name.
   */
  public static $_tableName = 'civicrm_contactinfo_history_address';

  /**
   * Should CiviCRM log any modifications to this table in the civicrm_log table.
   */
  public static $_log = FALSE;

  /**
   * History record ID.
   * @var int
   */
  public $id;

  /**
   * Original address ID from civicrm_address.
   * @var int
   */
  public $original_id;

  /**
   * FK to Contact.
   * @var int
   */
  public $contact_id;

  /**
   * Original contact ID before any merges.
   * @var int
   */
  public $orig_contact_id;

  /**
   * When this history record was created.
   * @var string
   */
  public $start_date;

  /**
   * When this history record was last modified.
   * @var string
   */
  public $modified_date;

  /**
   * When this address was superseded or deleted.
   * @var string
   */
  public $end_date;

  /**
   * Returns all the column names of this table.
   */
  public static function &fields() {
    if (!isset(Civi::$statics[__CLASS__]['fields'])) {
      Civi::$statics[__CLASS__]['fields'] = [
        'id' => [
          'name' => 'id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('ID'),
          'required' => TRUE,
          'where' => 'civicrm_contactinfo_history_address.id',
          'table_name' => 'civicrm_contactinfo_history_address',
          'entity' => 'ContactinfoHistoryAddress',
          'bao' => 'CRM_ContactinfoHistory_BAO_ContactinfoHistoryAddress',
          'localizable' => 0,
        ],
        'original_id' => [
          'name' => 'original_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Original Address ID'),
          'where' => 'civicrm_contactinfo_history_address.original_id',
          'table_name' => 'civicrm_contactinfo_history_address',
          'entity' => 'ContactinfoHistoryAddress',
          'bao' => 'CRM_ContactinfoHistory_BAO_ContactinfoHistoryAddress',
          'localizable' => 0,
        ],
        'contact_id' => [
          'name' => 'contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Contact ID'),
          'where' => 'civicrm_contactinfo_history_address.contact_id',
          'table_name' => 'civicrm_contactinfo_history_address',
          'entity' => 'ContactinfoHistoryAddress',
          'bao' => 'CRM_ContactinfoHistory_BAO_ContactinfoHistoryAddress',
          'localizable' => 0,
          'FKClassName' => 'CRM_Contact_DAO_Contact',
        ],
        'orig_contact_id' => [
          'name' => 'orig_contact_id',
          'type' => CRM_Utils_Type::T_INT,
          'title' => E::ts('Original Contact ID'),
          'description' => E::ts('Contact ID before any merges'),
          'where' => 'civicrm_contactinfo_history_address.orig_contact_id',
          'table_name' => 'civicrm_contactinfo_history_address',
          'entity' => 'ContactinfoHistoryAddress',
          'bao' => 'CRM_ContactinfoHistory_BAO_ContactinfoHistoryAddress',
          'localizable' => 0,
          'FKClassName' => 'CRM_Contact_DAO_Contact',
        ],
        'start_date' => [
          'name' => 'start_date',
          'type' => CRM_Utils_Type::T_TIMESTAMP,
          'title' => E::ts('Start Date'),
          'where' => 'civicrm_contactinfo_history_address.start_date',
          'table_name' => 'civicrm_contactinfo_history_address',
          'entity' => 'ContactinfoHistoryAddress',
          'bao' => 'CRM_ContactinfoHistory_BAO_ContactinfoHistoryAddress',
          'localizable' => 0,
        ],
        'modified_date' => [
          'name' => 'modified_date',
          'type' => CRM_Utils_Type::T_TIMESTAMP,
          'title' => E::ts('Modified Date'),
          'where' => 'civicrm_contactinfo_history_address.modified_date',
          'table_name' => 'civicrm_contactinfo_history_address',
          'entity' => 'ContactinfoHistoryAddress',
          'bao' => 'CRM_ContactinfoHistory_BAO_ContactinfoHistoryAddress',
          'localizable' => 0,
        ],
        'end_date' => [
          'name' => 'end_date',
          'type' => CRM_Utils_Type::T_TIMESTAMP,
          'title' => E::ts('End Date'),
          'where' => 'civicrm_contactinfo_history_address.end_date',
          'table_name' => 'civicrm_contactinfo_history_address',
          'entity' => 'ContactinfoHistoryAddress',
          'bao' => 'CRM_ContactinfoHistory_BAO_ContactinfoHistoryAddress',
          'localizable' => 0,
        ],
      ];
    }
    return Civi::$statics[__CLASS__]['fields'];
  }

  /**
   * Return a mapping from field-name to the corresponding key.
   */
  public static function &fieldKeys() {
    if (!isset(Civi::$statics[__CLASS__]['fieldKeys'])) {
      Civi::$statics[__CLASS__]['fieldKeys'] = array_flip(CRM_Utils_Array::collect('name', self::fields()));
    }
    return Civi::$statics[__CLASS__]['fieldKeys'];
  }

  /**
   * Returns the names of this table.
   */
  public static function getTableName() {
    return self::$_tableName;
  }

}
