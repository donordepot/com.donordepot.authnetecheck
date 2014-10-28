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
   * @param object $paymentProcessor
   * @param CRM_Core_Form $paymentForm
   * @param bool $force
   *
   * @return object
   * @static
   */
  static function &singleton($mode, &$paymentProcessor, &$paymentForm = NULL, $force = FALSE) {
      $processorName = $paymentProcessor['name'];
      if (!isset(self::$_singleton[$processorName]) || self::$_singleton[$processorName] === NULL) {
        self::$_singleton[$processorName] = new CRM_Core_Payment_AuthNetEcheck($mode, $paymentProcessor);
      }

      return self::$_singleton[$processorName];
  }

  /**
   * Submit a payment using Advanced Integration Method
   *
   * @param  array $params assoc array of input parameters for this transaction
   *
   * @return array the result in a nice formatted array (or an error object)
   * @public
   */
  function doDirectPayment(&$params) {

      $params = parent::doDirectPayment($params);

      // Fix the Payment Instrument on the Contribution.
      if (CRM_Utils_Array::value('contributionID', $params)) {

        $contribution = new CRM_Contribute_BAO_Contribution();
        $contribution->id = $params['contributionID'];

        if ($contribution->find(TRUE)) {

          $dateFields = array(
            'receive_date',
            'cancel_date',
            'receipt_date',
            'thankyou_date'
          );
          $this->fixDates($contribution, $dateFields);

          // Change the Payment Instrument ID.
          $option_group = new CRM_Core_OptionGroup();
          $instrument = $option_group->getValue('payment_instrument', 'EFT', 'name');
          $contribution->payment_instrument_id = $instrument;

          // Save the Contribution.
          $contribution->save();

        }

      }

      // Fix the Payment Instrument on the Recurring Contribution.
      if (CRM_Utils_Array::value('contributionRecurID', $params)) {

        $contribution = new CRM_Contribute_BAO_ContributionRecur();
        $contribution->id = $params['contributionRecurID'];

        if ($contribution->find(TRUE)) {

          $dateFields = array(
            'start_date',
            'create_date',
            'modified_date',
            'cancel_date',
            'end_date',
            'next_sched_contribution',
            'failure_retry_date',
          );
          $this->fixDates($contribution, $dateFields);

          // Change the Payment Instrument ID.
          $instrument = CRM_Core_OptionGroup::getValue('payment_instrument', 'EFT', 'name');
          $contribution->payment_instrument_id = $instrument;

          // Save the Contribution.
          $contribution->save();
        }

      }

      return $params;

  }

  /**
   * Submit an Automated Recurring Billing subscription
   *
   * @public
   */
  function doRecurPayment() {

    $template = CRM_Core_Smarty::singleton();

    $template->assign('paymentType', $this->_getParam('paymentType'));
    $template->assign('accountType', $this->_getParam('bank_account_type'));
    $template->assign('routingNumber', $this->_getParam('bank_identification_number'));
    $template->assign('accountNumber', $this->_getParam('bank_account_number'));
    $template->assign('nameOnAccount', $this->_getParam('account_holder'));
    $template->assign('echeckType', 'WEB');
    $template->assign('bankName', $this->_getParam('bank_name'));

    return parent::doRecurPayment();

  }

  function updateSubscriptionBillingInfo(&$message = '', $params = array()) {

    $template = CRM_Core_Smarty::singleton();

    $template->assign('paymentType', $this->_getParam('paymentType'));
    $template->assign('accountType', $params['bank_account_type']);
    $template->assign('routingNumber', $params['bank_identification_number']);
    $template->assign('accountNumber', $params['bank_account_number']);
    $template->assign('nameOnAccount', $params['account_holder']);
    $template->assign('echeckType', 'WEB');
    $template->assign('bankName', $params['bank_name']);

    return parent::updateSubscriptionBillingInfo($message, $params);

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

  /*
   * A cludgy hack to fix the dates to MySQL format.
   */
  private function fixDates(&$contribution, $dateFields) {

      foreach ($dateFields as $df) {

        if (!isset($contribution->{$df})) {
          continue;
        }

        $contribution->{$df} = CRM_Utils_Date::isoToMysql($contribution->{$df});

      }

  }


}
