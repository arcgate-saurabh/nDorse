<?php

App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel {

    var $actsAs = array("Multivalidatable");

    public function getHashPassword($password) {
        return AuthComponent::password($password);
    }

    public function beforeSave($options = array()) {
        if (!isset($this->data['User']['password_hashed'])) {
            $this->data['User']['password_hashed'] = false;
        }
        if (isset($this->data['User']['password']) && !$this->data['User']['password_hashed']) {
            $this->data['User']['password'] = AuthComponent::password($this->data['User']['password']);
        }
        return true;
    }

    public function afterSave($created, $options = array()) {
        parent::afterSave($created, $options);

        //updating authentication session
        App::uses('CakeSession', 'Model/Datasource');
        CakeSession::write('Auth', $this->findById(AuthComponent::user('id')));

        return true;
    }

    public function validate_passwords() {
        return $this->data[$this->alias]['password'] === $this->data[$this->alias]['confirm_password'];
    }

    //

    function checkUniqueEmail($data) {

        $isUnique = $this->find(
                'first', array(
            'fields' => array(
                'User.id'
            ),
            'conditions' => array(
                'User.email' => $data['email']
            )
                )
        );

        if (!empty($isUnique)) {

            if ($this->authUserId == $isUnique['User']['id']) {
                return true; //Allow update
            } else {
                return false; //Deny update
            }
        } else {
            return true; //If there is no match in DB allow anyone to change
        }
    }

    public function email_registered($params) {
        return $this->find('count', array('conditions' => array('User.email' => $params['email']))) ? true : false;
    }

    public function validate_password() {
        return $this->data['User']['password'] === $this->data['User']['confirm_password'];
    }

    public $validationSets = array(
        "inviteOnly" => array(
            'email' => array(
                'ruleRequired' => array(
                    'rule' => 'notEmpty',
                    'message' => 'Please enter email address to invite a user'
                ),
                'ruleEmail' => array(
                    'rule' => 'email',
                    'message' => 'Please enter valid email address'
                ),
                'isUnique' => array(
                    'rule' => 'isUnique',
                    'message' => 'This email is already registered.'
                )
            )
        ),
        "register" => array(
//            'fname' => array(
//                'ruleAlphabets' => array(
//                    'rule' => '/^[a-z .,-\/ ]+$/i',
//                    'message' => 'First name must contain letters and spaces only.'
//                )
//            ),
//            'lname' => array(
//                'ruleAlphabets' => array(
////                    'rule' => '/^[a-z ]+$/i',
//                    'rule' => '/^[a-z .,-\/ ]+$/i',
//                    'message' => 'Last name must contain letters and spaces only.'
//                )
//            ),
            'email' => array(
                'ruleRequired' => array(
                    'rule' => 'notBlank',
                    "required" => true,
                    'message' => 'Please enter email address'
                ),
                'ruleEmail' => array(
                    'rule' => 'email',
                    'message' => 'Please enter valid email address'
                ),
                'isUnique' => array(
                    'rule' => 'isUnique',
                    'message' => 'This email is already registered.',
                    'on' => 'create',
                )
            ),
            'image' => array(
                'ruleValid' => array(
                    'rule' => array('validateImage'),
                )
            ),
        ),
        'login' => array('email' => array(
                'ruleRequired' => array(
                    'rule' => 'notBlank',
                    "required" => true,
                    'message' => 'Please enter email address'
                ),
                'ruleEmail' => array(
                    'rule' => 'email',
                    'message' => 'Please enter valid email address'
                ),
//                'isRegistered' => array(
//                    'rule' => 'email_registered',
//                    'message' => 'This email is not registered.'
//                )
            )
        ),
        "edit" => array(
//            'fname' => array(
//                'ruleAlphabets' => array(
//                    'rule' => '/^[a-z ]+$/i',
//                    'message' => 'First name must contain letters and spaces only.'
//                )
//            ),
//            'lname' => array(
//                'ruleAlphabets' => array(
//                    'rule' => '/^[a-z ]+$/i',
//                    'message' => 'Last name must contain letters and spaces only.'
//                )
//            ),
            'image' => array(
                'ruleValid' => array(
                    'rule' => array('validateImage'),
                )
            )
        ),
        'forgot_password' => array(
            'email' => array(
                'ruleRequired' => array(
                    'rule' => 'notBlank',
                    'message' => 'E-mail can not be empty.'
                ),
                'ruleEmail' => array(
                    'rule' => 'email',
                    'message' => 'Invalid email address. Please check.'
                ),
                'isNotRegistered' => array(
                    'rule' => array('email_registered'),
                    'message' => 'This email is not registered.'
                )
            )
        ),
        'reset_password' => array(
            'password' => array(
                'ruleRequired' => array(
                    'rule' => 'notBlank',
                    'message' => 'Enter password.'
                ),
                'ruleLength' => array(
                    'rule' => array('minLength', 8),
                    'message' => 'Password must be 8 characters long.'
                )
            ),
            'confirm_password' => array(
                'ruleRequired' => array(
                    'rule' => 'notBlank',
                    'message' => 'Confirm password.'
                ),
                'ruleMatch' => array(
                    'rule' => array('validate_passwords'),
                    'message' => 'Password and confirm password does not match.'
                )
            )
        ),
        'change_password' => array(
            'password' => array(
                'ruleRequired' => array(
                    'rule' => 'notBlank',
                    'message' => 'Please enter password.'
                )
            ),
            'confirm_password' => array(
                'ruleRequired' => array(
                    'rule' => 'notBlank',
                    'message' => 'lease enter confirm password.'
                ),
                'ruleMatch' => array(
                    'rule' => array('validate_passwords'),
                    'message' => 'Password and confirm password does not match.'
                )
            ),
            'security_answer' => array(
                'ruleRequired' => array(
                    'rule' => 'notBlank',
                    'message' => 'Security answer can not be empty.'
                ),
                'verifySecurityAnswer' => array(
                    'rule' => array('verify_security_answer'),
                    'message' => 'Security answer mismatched'
                )
            )
        )
    );

}

?>