<?php

App::import('Vendor', 'Braintree', array('file' => 'Braintree/lib' . DS . 'Braintree.php'));

class BraintreeComponent extends Component {
    
     var $uses = array("User", "Organization", "Subscription");

    public function __construct() {
        Braintree_Configuration::environment(Configure::read('Braintree.env'));
        Braintree_Configuration::merchantId(Configure::read('Braintree.merchantId'));
        Braintree_Configuration::publicKey(Configure::read('Braintree.publicKey'));
        Braintree_Configuration::privateKey(Configure::read('Braintree.privateKey'));
    }
    
    public function cancelSubscription($btId, $organizationId) {
        $loggedinUser = AuthComponent::user();
         $this->Transaction = ClassRegistry::init('Transaction');
        $success = true;
        
        $transaction = array(
                   'organization_id' => $organizationId,
                   'user_id' => $loggedinUser['id'],
                   'type' => 'cancel',
                   'user_diff' => 0,
                   'bt_subscription_id' => $btId,
                   'amount' => 0,
                   'status' => "canceled"
               );

        $this->Transaction->save($transaction);
        $transactionId = $this->Transaction->id;
        

        $result = Braintree_Subscription::cancel($btId);
        if ($result->success) {
            $success = true;
        } else {
            $this->Transaction->delete();
            $success = false;
        }
                    
        return  $success;
    }

}

?>
