<?php

App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class Subscription extends AppModel {

    public $belongsTo = array(
        'Organization' => array(
            'className' => 'Organization',
            'foreignKey' => 'organization_id'
        ),
        'SubscriptionPlan' => array(
            'className' => 'SubscriptionPlan',
            'foreignKey' => 'plan_id'
        ),
    );
}
?>