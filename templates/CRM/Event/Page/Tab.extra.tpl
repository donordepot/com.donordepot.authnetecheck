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

if (!cj('select[name="payment_processor_id"]').hasClass('fee-block')) {

    var civicrmBuildFeeBlock = buildFeeBlock;

    function buildFeeBlock( eventId, discountId ) {
      var dataUrl = {/literal}"{crmURL p=$urlPath h=0 q='snippet=4'}";
                  dataUrl = dataUrl + '&qfKey=' + '{$qfKey}'

      {if $urlPathVar}
      dataUrl = dataUrl + '&' + '{$urlPathVar}'
      {/if}

      {literal}

      if ( !eventId ) {
        var eventId  = document.getElementById('event_id').value;
      }

      if ( eventId) {
        dataUrl = dataUrl + '&eventId=' + eventId;
      } else {
                          cj('#eventFullMsg').hide( );
        cj('#feeBlock').html('');
        return;
      }

      var participantId  = "{/literal}{$participantId}{literal}";

      if ( participantId ) {
        dataUrl = dataUrl + '&participantId=' + participantId;
      }

      if ( discountId ) {
        dataUrl = dataUrl + '&discountId=' + discountId;
      }

      var payment_processor_id = cj('select[name="payment_processor_id"]').val();

      if ( payment_processor_id ) {
        dataUrl = dataUrl + '&payment_processor_id=' + payment_processor_id;
      }

      cj.ajax({
        url: dataUrl,
        async: false,
        global: false,
        success: function ( html ) {
            cj("#feeBlock").html( html );
        }
        });

          cj("#feeBlock").ajaxStart(function(){
              cj(".disable-buttons input").attr('disabled', true);
          });

          cj("#feeBlock").ajaxStop(function(){
              cj(".disable-buttons input").attr('disabled', false);
          });

          //show event real full as well as waiting list message.
          if ( cj("#hidden_eventFullMsg").val( ) ) {
            cj( "#eventFullMsg" ).show( ).html( cj("#hidden_eventFullMsg" ).val( ) );
          } else {
            cj( "#eventFullMsg" ).hide( );
          }
    }

    cj('select[name="payment_processor_id"]').addClass('fee-block').change( function() {

      //build discount block
      var eventId = 0;
      if ( document.getElementById('event_id') ) {
        eventId  = document.getElementById('event_id').value;
      }

      var discountId = 0;
      if ( document.getElementById('discount_id') ) {
        var discountId  = document.getElementById('discount_id').value;
      }

      buildFeeBlock( eventId, discountId );

    });

}

</script>
{/literal}
