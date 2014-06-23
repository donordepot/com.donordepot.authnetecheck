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

  // Loop through the fields to make changes.
  $changed = 0;
  foreach ($form->_elements as $key => &$element) {

    // Stop the loop if everything is done.
    if ($changed == 3) {
      break;
    }

    // If there is no "name" attribute, continue.
    if (empty($element->_attributes['name'])) {
      continue;
    }

    // Change the label based on the name of the field.
    if ($element->_attributes['name'] == 'bank_identification_number') {

      $element->_label = ts('Routing Number');
      $changed++;

    }
    else if ($element->_attributes['name'] == 'account_holder') {

      $element->_label = ts('Name on Account');
      $changed++;

    }
    else if ($element->_attributes['name'] == 'bank_account_number') {

      $element->_label = ts('Account Number');
      $changed++;

    }

  }

  // Build the Account Type Field.
  $form->_paymentFields['bank_account_type'] = array(
    'htmlType' => 'select',
    'name' => 'bank_account_type',
    'title' => ts('Account Type'),
    'cc_field' => TRUE,
    'attributes' => array(
      '' => ts('- select -'),
      'checking' => ts('Checking'),
      'businesschecking' => ts('Business Checking'),
      'savings' => ts('Savings'),
    ),
    'is_required' => TRUE,
  );

  // Add the Account Type Drop-Down
  $field = $form->_paymentFields['bank_account_type'];
  $form->add($field['htmlType'],
    $field['name'],
    $field['title'],
    $field['attributes'],
    $useRequired ? $field['is_required'] : FALSE
  );

}

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function authnetecheck_civicrm_config(&$config) {
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
  return _authnetecheck_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function authnetecheck_civicrm_disable() {
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
    'name' => 'Authorize.net eCheck.Net',
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
