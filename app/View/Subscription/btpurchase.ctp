<?php echo $this->Html->script('braintree-2.24.1.min.js'); ?>
<?php
//pr($plans);die;
?>

<div class="row">
  <div class="small-6 large-6 columns"> 
    <div class="col-md-5 col-md-offset-3">
        <div class="panel panel-default sub-type" >
            <h4>Subscription type and rates</h4>
            <ul>
           <?php foreach($plans as $key => $plan) { 
                echo "<li>" . $plan['name'] . ": $" . $plan['rate'] . " per user</li>";
             } ?>
                </ul>
            
        </div>
      <div class="panel panel-default" style="background-color:transparent; padding:10px 0">
        <form id="checkout" action="<?php echo Router::url('/', true); ?>subscription/btcheckout" method="post">
          <!--<label>Select subscription plan : </label> -->
          <span class="radio" style="padding:0 0 0 10px; margin-top:0;">
           <span class="help-block text-muted small" style="margin-top:0px;">Select subscription plan : </span>
          <div class="input radio">
            
              <?php foreach($plans as $key => $plan) { ?>
              <div class="col-md-3" style="padding:0 0">
              	<input type="radio" checked="checked" value="<?php echo $key; ?>" id="subscriptionPlan<?php echo $key; ?>" name="plan">
             	<label for="subscriptionPlan<?php echo $key; ?>"><?php echo $plan['name']; ?></label>
              </div>
              <?php } ?>
           
          </div>
          </span> 
          
          <!--<input type="radio" name="plan" value="monthly" checked> <label for="plan">Monthly </label><br>--> 
          <br>
          <div class="col-md-12 col-sm-12 col-xs-12"> <span class="help-block text-muted small">Number of Users</span>
            <input type="text" name="usercount" class="form-control js_subUserCount">
          </div>
          <div class="col-md-12 col-sm-12 col-xs-12"> <span class="help-block text-muted small">Amount to pay</span>
            <input type="text" name="price" class="form-control" disabled="disabled" id="js_price">
          </div>
          <div class="col-md-12 col-sm-12 col-xs-12"> <span class="help-block text-muted small">Card Number</span>
            <input data-braintree-name="number"  class="form-control"  >
          </div>
          <div class="col-md-3 col-sm-3 col-xs-3"> <span class="help-block text-muted small">Expiry Month</span>
            <input data-braintree-name="expiration_month"  class="form-control" >
          </div>
          <div class="col-md-3 col-sm-3 col-xs-3"> <span class="help-block text-muted small">Expiry Year</span>
            <input data-braintree-name="expiration_year"  class="form-control " >
          </div>
          <div class="col-md-3 col-sm-3 col-xs-3"> <span class="help-block text-muted small">C V V</span>
            <input data-braintree-name="cvv"  class="form-control">
          </div>
          <div class="col-md-3 col-sm-3 col-xs-3"> <span class="help-block text-muted small">&nbsp;</span><?php echo $this->Html->image('card.png'); ?> </div>
          <div class="clearfix"></div>
          <div class="col-md-9 col-sm-9 col-xs-9"> <span class="help-block text-muted small">Card Holder Name</span>
            <input data-braintree-name="cardholder_name"  class="form-control js_cardHolder" >
          </div>
          <div class="col-md-3 col-sm-3 col-xs-3"> <span class="help-block text-muted small">&nbsp;</span>
            <input type="submit" id="submit" value="Pay Now" class="btn btn-success js_subPurchase" >
          </div>
          <input type="hidden" name="btog" value="<?php echo $btog; ?>">
          <input type="hidden" id="js_maxSubUsers" value="<?php echo $maxUsers; ?>">
          <div class="clearfix"></div>
        </form>
      </div>
    </div>
    <div class="clearfix"></div>
    
    <div class="col-md-5 col-md-offset-3 text-center hidden js_subPurchaseLodder"><img src="<?php echo Router::url('/', true); ?>img/ajax-loader.gif" alt="" /></div><br />

  </div>
  <div class="small-6 large-6 columns"> </div>
</div>

<script>

var clientToken = "<?php echo $clientToken; ?>";
var plansJson = '<?php echo json_encode($plans); ?>';
var plans = $.parseJSON(plansJson);

$(document).ready(function () {
$( ".js_subUserCount" ).focusout(function() {
    calculatePrice();
});

$( "form#checkout :input" ).focusout(function() {
   checkEmpty($(this));
});

$( "form#checkout :input" ).keyup(function() {
     $(this).parent().find("label.error").remove();
});

$('input[type=radio][name=plan]').change(function() {
    calculatePrice();
});
});

function checkEmpty(obj) {
//    console.log(obj.parent().find("label.error"));
     obj.parent().find("label.error").remove();
     
     var elementId = obj.attr('id');
     
     if(elementId == 'js_price') {
         return true;
     }
     
    var val = obj.val();
    val = $.trim(val);   
//    console.log(val);
    if(val == "") {
        obj.parent().append('<label class="error" style="display: inline-block;"> Required</label>');
        return false;
    }
    
    return true;
}

function calculatePrice() {
    var userCount = $( ".js_subUserCount" ).val();
    userCount = $.trim(userCount);
    
    var planId = $('input[name=plan]:checked', '#checkout').val();
    if(userCount != "") {
        var price  = plans[planId]['rate'] * userCount;
        $("#js_price").val("$" + price);
    }
}

braintree.setup(clientToken, "custom", {id: "checkout"});
$(document).on("submit", "#checkout", function () {
    var isSubmit = true;
    $("form#checkout :input[type!='hidden']").each(function(){
        if(!checkEmpty($(this))) {
            isSubmit = false;
        }
    });
    
    if(!isSubmit) {
        return false;
    }
    
    $(".js_subPurchaseLodder").removeClass("hidden");
});



//braintree.setup(clientToken, "custom", {
//        id: "checkout",
//        onReady: function (integration) {
//            checkout = integration;
//        },
//        hostedFields: {
//          number: {
//            selector: "#card-number"
//          },
//          cvv: {
//            selector: "#cvv"
//          },
//          expirationDate: {
//            selector: "#expiration-date"
//          }
//        },
//			    styles: {
//			      	'.invalid': {
//			        	'color': '#D0041D'
//			      	}
//			    },
//				onFieldEvent: function (event) {
//		            if (event.type === "focus") {
//		            } else if (event.type === "blur") {
//		                BTHFValidate(event);
//		            } else if (event.type === "fieldStateChange") {
//		                if(!event.isValid && !event.isFocused) {
//		                    BTHFValidate(event);
//		                } else {
//		                    //hideError4BT();
//		                }
//		            }
//		        },
//      });


//braintree.setup(clientToken, "paypal", {
//  container: "paypal-container",
//  displayName : "Silly5",
//  onPaymentMethodReceived: function (obj) {
//    console.log('Paypal', obj.nonce);
//    $("input[name=payment_method_nonce]").val(obj.nonce);
//    //$('#checkout').append('<input type="text" name="payment_method_nonce" value="'+obj.nonce+'">');
//    $('#checkout').submit();
//  }
//});
</script>