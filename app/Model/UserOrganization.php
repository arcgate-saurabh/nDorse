<?php

App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class UserOrganization extends AppModel {

    public $belongsTo = array(
        'Organization' => array(
            'className' => 'Organization',
            'foreignKey' => 'organization_id'
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id'
        )
    );
    public $validate = array(
        'organization_id' => array(
            'ruleRequired' => array(
                'rule' => 'notBlank',
                "required" => true,
                'message' => 'Organization Id can not be empty.'
            )
        ),
        'user_id' => array(
            'ruleRequired' => array(
                'rule' => 'notBlank',
                "required" => true,
                'message' => 'User Id can not be empty.'
            )
        ),
        'pool_type' => array(
            'ruleRequired' => array(
                'rule' => 'notBlank',
                "required" => true,
                'message' => 'Pool type can not be empty.'
            )
        ),
    );

}

?>