<?php

App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class EndorseAttachment extends AppModel {

    public $validate = array(
        'image' => array(
            'ruleValid' => array(
                'rule' => array('validateImage'),
            )
        )
    );

}

?>