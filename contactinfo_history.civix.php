<?php

// AUTO-GENERATED FILE -- Civix may overwrite any changes made to this file

/**
 * The ExtensionUtil class provides small stubs for accessing resources of this extension.
 */
class CRM_ContactinfoHistory_ExtensionUtil {
  const SHORT_NAME = 'contactinfo_history';
  const LONG_NAME = 'com.moellman.contactinfo_history';
  const CLASS_PREFIX = 'CRM_ContactinfoHistory';

  /**
   * Translate a string using the extension's domain.
   */
  public static function ts($text, $params = []) {
    if (!array_key_exists('domain', $params)) {
      $params['domain'] = [self::LONG_NAME, NULL];
    }
    return ts($text, $params);
  }

  /**
   * Get the URL of a resource file (in this extension).
   */
  public static function url($file = NULL) {
    if ($file === NULL) {
      return rtrim(CRM_Core_Resources::singleton()->getUrl(self::LONG_NAME), '/');
    }
    return CRM_Core_Resources::singleton()->getUrl(self::LONG_NAME, $file);
  }

  /**
   * Get the path of a resource file (in this extension).
   */
  public static function path($file = NULL) {
    return __DIR__ . ($file === NULL ? '' : (DIRECTORY_SEPARATOR . $file));
  }

  /**
   * Get the name of a class within this extension.
   */
  public static function findClass($suffix) {
    return self::CLASS_PREFIX . '_' . str_replace('\\', '_', $suffix);
  }

}

use CRM_ContactinfoHistory_ExtensionUtil as E;

/**
 * (Delegated) Implements hook_civicrm_xmlMenu().
 */
function _contactinfo_history_civix_civicrm_xmlMenu(&$files) {
  foreach (glob(__DIR__ . '/xml/Menu/*.xml') as $file) {
    $files[] = $file;
  }
}

/**
 * (Delegated) Implements hook_civicrm_config().
 */
function _contactinfo_history_civix_civicrm_config(&$config = NULL) {
  static $configured = FALSE;
  if ($configured) {
    return;
  }
  $configured = TRUE;

  $template = CRM_Core_Smarty::singleton();
  $extRoot = __DIR__ . DIRECTORY_SEPARATOR;
  $extDir = $extRoot . 'templates';

  if (is_dir($extDir)) {
    $template->addTemplateDir($extDir);
  }

  $include_path = $extRoot . PATH_SEPARATOR . get_include_path();
  set_include_path($include_path);
}

/**
 * (Delegated) Implements hook_civicrm_install().
 */
function _contactinfo_history_civix_civicrm_install() {
  _contactinfo_history_civix_civicrm_config();
  if ($upgrader = _contactinfo_history_civix_upgrader()) {
    $upgrader->onInstall();
  }
}

/**
 * (Delegated) Implements hook_civicrm_uninstall().
 */
function _contactinfo_history_civix_civicrm_uninstall() {
  _contactinfo_history_civix_civicrm_config();
  if ($upgrader = _contactinfo_history_civix_upgrader()) {
    $upgrader->onUninstall();
  }
}

/**
 * (Delegated) Implements hook_civicrm_enable().
 */
function _contactinfo_history_civix_civicrm_enable() {
  _contactinfo_history_civix_civicrm_config();
  if ($upgrader = _contactinfo_history_civix_upgrader()) {
    if (is_callable([$upgrader, 'onEnable'])) {
      $upgrader->onEnable();
    }
  }
}

/**
 * (Delegated) Implements hook_civicrm_disable().
 */
function _contactinfo_history_civix_civicrm_disable() {
  _contactinfo_history_civix_civicrm_config();
  if ($upgrader = _contactinfo_history_civix_upgrader()) {
    if (is_callable([$upgrader, 'onDisable'])) {
      $upgrader->onDisable();
    }
  }
}

/**
 * (Delegated) Implements hook_civicrm_upgrade().
 */
function _contactinfo_history_civix_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  if ($upgrader = _contactinfo_history_civix_upgrader()) {
    return $upgrader->onUpgrade($op, $queue);
  }
}

/**
 * @return CRM_ContactinfoHistory_Upgrader|NULL
 */
function _contactinfo_history_civix_upgrader() {
  if (!file_exists(__DIR__ . '/CRM/ContactinfoHistory/Upgrader.php')) {
    return NULL;
  }
  return CRM_ContactinfoHistory_Upgrader::instance();
}

/**
 * (Delegated) Implements hook_civicrm_entityTypes().
 */
function _contactinfo_history_civix_civicrm_entityTypes(&$entityTypes) {
  $entityTypes = array_merge($entityTypes, [
    'CRM_ContactinfoHistory_DAO_ContactinfoHistoryAddress' => [
      'name' => 'ContactinfoHistoryAddress',
      'class' => 'CRM_ContactinfoHistory_DAO_ContactinfoHistoryAddress',
      'table' => 'civicrm_contactinfo_history_address',
    ],
    'CRM_ContactinfoHistory_DAO_ContactinfoHistoryEmail' => [
      'name' => 'ContactinfoHistoryEmail',
      'class' => 'CRM_ContactinfoHistory_DAO_ContactinfoHistoryEmail',
      'table' => 'civicrm_contactinfo_history_email',
    ],
    'CRM_ContactinfoHistory_DAO_ContactinfoHistoryPhone' => [
      'name' => 'ContactinfoHistoryPhone',
      'class' => 'CRM_ContactinfoHistory_DAO_ContactinfoHistoryPhone',
      'table' => 'civicrm_contactinfo_history_phone',
    ],
  ]);
}
