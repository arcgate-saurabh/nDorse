<?php

App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class Endorsement extends AppModel {

    public $hasMany = array(
        'EndorseAttachments' => array(
            'className' => 'EndorseAttachments',
            'order' => 'EndorseAttachments.created ASC'
        ),
        'EndorseCoreValues' => array(
            'className' => 'EndorseCoreValues',
            'order' => 'EndorseCoreValues.id DESC'
        ),
        'EndorseHashtag' => array(
            'className' => 'EndorseHashtag',
            'order' => 'EndorseHashtag.id DESC'
        ),
        'EndorseReplies' => array(
            'className' => 'EndorseReplies'
        ) 
    );
    public $validate = array(
        'endorsed_id' => array(
            'ruleRequired' => array(
                'rule' => 'notBlank',
                "required" => true,
                'message' => 'Endorsee cannot be blank.'
            )
        ),
        'endorser_id' => array(
            'ruleRequired' => array(
                'rule' => 'notBlank',
                "required" => true,
                'message' => 'Endorser cannot be blank.'
            )
        ),
        'organization_id' => array(
            'ruleRequired' => array(
                'rule' => 'notBlank',
                "required" => true,
                'message' => 'Organization Id cannot be blank.'
            )
        )
    );

}

?>