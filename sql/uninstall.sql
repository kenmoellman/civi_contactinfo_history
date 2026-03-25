-- Uninstall: drop history tables
-- Triggers are removed by CRM_Core_DAO::triggerRebuild() in the Upgrader

DROP TABLE IF EXISTS civicrm_contactinfo_history_address;
DROP TABLE IF EXISTS civicrm_contactinfo_history_email;
DROP TABLE IF EXISTS civicrm_contactinfo_history_phone;
