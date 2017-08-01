<?php

require_once 'authnetecheck.civix.php';


/**
 * Implementation of hook_civicrm_buildForm
 */
function authnetecheck_civicrm_buildForm($formName, &$form) {

  // If the form does not have payment fields, return.
  if (empty($form->_paymentFields)) {
    return;
  }
  
  // Change the label based on the name of the field.
  if (isset($form->_elementIndex['bank_identification_number'])) {
    $index = $form->_elementIndex['bank_identification_number'];
    $form->_elements[$index]->_label = ts('Routing Number');
  }

  if (isset($form->_elementIndex['account_holder'])) {
    $index = $form->_elementIndex['account_holder'];
    $form->_elements[$index]->_label = ts('Name on Account');
  }

  if (isset($form->_elementIndex['bank_account_number'])) {
    $index = $form->_elementIndex['bank_account_number'];
    $form->_elements[$index]->_label = ts('Account Number');
  }
  
}

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function authnetecheck_civicrm_config(&$config) {

  if (!isset($config->defaultContactStateProvince)) {
    $config->defaultContactStateProvince = '';
  }

  _authnetecheck_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function authnetecheck_civicrm_xmlMenu(&$files) {
  _authnetecheck_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function authnetecheck_civicrm_install() {
  return _authnetecheck_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function authnetecheck_civicrm_uninstall() {
  return _authnetecheck_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function authnetecheck_civicrm_enable() {

  // See if there is an Existing 'EFT' Payment Instrument.
  $value = new CRM_Core_DAO_OptionValue();
  $value->option_group_id = 10;
  $value->name = 'EFT';

  // If one exists, set it to reserved so it cannot be disabled nor deleted.
  if ($value->find(TRUE)) {
    $value->is_active = TRUE;
    $value->is_reserved = TRUE;
    $value->save();
  }
  else {

    // Since a 'EFT' Payment Instrument no longer exists, Create one.
    $params = array(
      'option_group_id' => 10,
      'label' => 'EFT',
      'name' => 'EFT',
      'is_reserved' => TRUE,
      'is_active' => TRUE,
    );

    // Set the Group ID.
    $groupParams = array(
      'id' => 10,
    );

    // The Action on the value is to add +1 to the highest value.
    $action = CRM_Core_Action::ADD;

    // Creating a new value, so this can be 0.
    $optionValueID = 0;

    // Save the new Option Value.
    CRM_Core_OptionValue::addOptionValue($params, $groupParams, $action, $optionValueID);

  }

  return _authnetecheck_civix_civicrm_enable();

}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function authnetecheck_civicrm_disable() {

  // Find the Existing 'EFT' Payment Instrument.
  $value = new CRM_Core_DAO_OptionValue();
  $value->option_group_id = 10;
  $value->name = 'EFT';

  // If a Payment Instrument can be found,
  // remove the reserved option.
  if ($value->find(TRUE)) {
    $value->is_reserved = FALSE;
    $value->save();
  }

  return _authnetecheck_civix_civicrm_disable();

}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function authnetecheck_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _authnetecheck_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function authnetecheck_civicrm_managed(&$entities) {

  $entities[] = array(
    'module' => 'com.donordepot.authnetecheck',
    'name' => 'Authorize.Net eCheck.Net',
    'entity' => 'PaymentProcessorType',
    'params' => array(
      'version' => 3,
      'name' => 'AuthorizeNeteCheck',
      'title' => 'Authorize.net eCheck.Net',
      'class_name' => 'Payment_AuthNetEcheck',
      'billing_mode' => 1,
      'user_name_label' => 'API Login',
      'password_label' => 'Payment Key',
      'signature_label' => 'MD5 Hash',
      'url_site_default'=> 'https://secure.authorize.net/gateway/transact.dll',
      'url_recur_default' => 'https://api.authorize.net/xml/v1/request.api',
      'url_site_test_default' => 'https://test.authorize.net/gateway/transact.dll',
      'url_recur_test_default' => 'https://apitest.authorize.net/xml/v1/request.api',
      'is_recur' => 1,
      'payment_type' => 2,
    ),
  );

  return _authnetecheck_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function authnetecheck_civicrm_caseTypes(&$caseTypes) {
  _authnetecheck_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function authnetecheck_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _authnetecheck_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
