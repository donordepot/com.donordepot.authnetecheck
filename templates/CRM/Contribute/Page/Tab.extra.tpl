{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
{literal}
<script type="text/javascript">

function buildPaymentBlock( type ) {
  if ( type == 0 ) {
    if (cj("#CreditCard .crm-accordion-body").length) {
      cj("#CreditCard .crm-accordion-body").html('');
    }
    return;
  }

  var dataUrl = {/literal}"{crmURL p=$urlPath h=0 q='snippet=4'}"{literal};

  {/literal}
  {if $urlPathVar}
    dataUrl = dataUrl + '&' + '{$urlPathVar}'
  {/if}

  dataUrl = dataUrl + '&formType=CreditCard&mode=live&payment_processor_id=' + type

  {if $qfKey}
    dataUrl = dataUrl + '&qfKey=' + '{$qfKey}'
  {/if}
  {literal}

  var fname = '.crm-CreditCard-accordion .crm-accordion-body';
  var response = cj.ajax({
                        url: dataUrl,
                        async: false
                        }).responseText;

                        console.log(fname);
                        console.log(response);

  cj( fname ).html( response );
}

if (!cj('select[name="payment_processor_id"]').hasClass('payment-block')) {

  cj( function() {
      var processorTypeObj = cj('select[name="payment_processor_id"]');

      var processorTypeValue = processorTypeObj.val( );

      cj('select[name="payment_processor_id"]').addClass('payment-block');

      if ( processorTypeValue ) {
        buildPaymentBlock( processorTypeValue );
      }

      cj('select[name="payment_processor_id"]').change( function() {
        buildPaymentBlock( cj(this).val() );
      });
  });

}

</script>
{/literal}
