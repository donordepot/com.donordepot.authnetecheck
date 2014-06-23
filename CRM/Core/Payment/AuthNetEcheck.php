<?php

class CRM_Core_Payment_AuthNetEcheck extends CRM_Core_Payment_AuthorizeNet {

  /**
   * We only need one instance of this object. So we use the singleton
   * pattern and cache the instance in this variable
   *
   * @var object
   * @static
   */
  static private $_singleton = NULL;

  /**
   * Constructor
   *
   * @param string $mode the mode of operation: live or test
   *
   * @return void
   */
  function __construct($mode, &$paymentProcessor) {
      parent::__construct($mode, $paymentProcessor);
      $this->_setParam('paymentType', 'ECHECK');
      $this->_processorName = ts('Authorize.net eCheck.net');
  }

  /**
   * singleton function used to manage this object
   *
   * @param string $mode the mode of operation: live or test
   *
   * @return object
   * @static
   *
   */
  static function &singleton($mode, &$paymentProcessor) {
      $processorName = $paymentProcessor['name'];
      if (!isset(self::$_singleton[$processorName]) || self::$_singleton[$processorName] === NULL) {
        self::$_singleton[$processorName] = new CRM_Core_Payment_AuthNetEcheck($mode, $paymentProcessor);
      }

      return self::$_singleton[$processorName];
  }

  function _getAuthorizeNetFields() {
      $fields = parent::_getAuthorizeNetFields();

      $fields['x_method'] = $this->_getParam('paymentType');
      $fields['x_bank_aba_code'] = $this->_getParam('bank_identification_number');
      $fields['x_bank_acct_num'] = $this->_getParam('bank_account_number');
      $fields['x_bank_acct_type'] = strtoupper($this->_getParam('bank_account_type'));
      $fields['x_bank_name'] = $this->_getParam('bank_name');
      $fields['x_bank_acct_name'] = $this->_getParam('account_holder');
      $fields['x_echeck_type'] = 'WEB';

      $fields['x_relay_response'] = 'FALSE';

      // request response in CSV format
      $fields['x_delim_data'] = 'TRUE';
      $fields['x_delim_char'] = ',';
      $fields['x_encap_char'] = '"';

      return $fields;
  }

}
