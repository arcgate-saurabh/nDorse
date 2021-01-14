<?php

App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class Verification extends AppModel {

    public $validate = array(
        'email' => array(
            'ruleRequired' => array(
                'rule' => 'notBlank',
                "required" => true,
                'message' => 'Enter email address.'
            ),
            'ruleEmail' => array(
                'rule' => 'email',
                'message' => 'Invalid email address. Please check.'
            ),
            'isUnique' => array(
                'rule' => 'isUnique',
                'message' => 'This email is already registered.'
            )
        ),
        'password' => array(
            'ruleRequired' => array(
                'rule' => 'notBlank',
                "required" => true,
                'message' => 'Enter password.'
            ),
            'ruleLength' => array(
                'rule' => array('minLength', 8),
                'message' => 'Password must be 8 characters long.'
            )
        )
    );

    public function beforeSave($options = array()) {
        if (isset($this->data['Verification']['password'])) {
            $this->data['Verification']['password'] = AuthComponent::password($this->data['Verification']['password']);
        }
        return true;
    }

}

?>