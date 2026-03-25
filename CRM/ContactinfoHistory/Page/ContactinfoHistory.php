<?php

use CRM_ContactinfoHistory_ExtensionUtil as E;

class CRM_ContactinfoHistory_Page_ContactinfoHistory extends CRM_Core_Page {

  public function run() {
    $contactId = CRM_Utils_Request::retrieve('cid', 'Positive', CRM_Core_DAO::$_nullObject, TRUE);

    // Check permission
    if (!CRM_Contact_BAO_Contact_Permission::allow($contactId, CRM_Core_Permission::VIEW)) {
      CRM_Core_Error::statusBounce(E::ts('You do not have permission to view this contact.'));
    }

    // Get history data
    $addressHistory = CRM_ContactinfoHistory_BAO_ContactinfoHistoryAddress::getHistory($contactId);
    $emailHistory = CRM_ContactinfoHistory_BAO_ContactinfoHistoryEmail::getHistory($contactId);
    $phoneHistory = CRM_ContactinfoHistory_BAO_ContactinfoHistoryPhone::getHistory($contactId);

    // Check manage permission
    $canManage = CRM_Core_Permission::check('administer CiviCRM')
              || CRM_Core_Permission::check('manage contactinfo history');

    $this->assign('contactId', $contactId);
    $this->assign('addressHistory', $addressHistory);
    $this->assign('emailHistory', $emailHistory);
    $this->assign('phoneHistory', $phoneHistory);
    $this->assign('canManage', $canManage);

    parent::run();
  }

}
