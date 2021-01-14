<?php

App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class Post extends AppModel {

    public $hasMany = array(
        'PostAttachments' => array(
            'className' => 'PostAttachments',
            'order' => 'PostAttachments.created ASC'
        )
    );
   

//    public $validate = array(
//        'post_id' => array(
//            'ruleRequired' => array(
//                'rule' => 'notBlank',
//                "required" => true,
//                'message' => 'Endorsee cannot be blank.'
//            )
//        ),
//        'endorser_id' => array(
//            'ruleRequired' => array(
//                'rule' => 'notBlank',
//                "required" => true,
//                'message' => 'Endorser cannot be blank.'
//            )
//        ),
//        'organization_id' => array(
//            'ruleRequired' => array(
//                'rule' => 'notBlank',
//                "required" => true,
//                'message' => 'Organization Id cannot be blank.'
//            )
//        )
//    );
}

?>