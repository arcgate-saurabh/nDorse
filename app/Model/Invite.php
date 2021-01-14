<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');
class Invite extends AppModel 
{
				public $validate = array(
								'email' => array(
                'ruleRequired'=> array(
                    'rule' => 'notBlank',
																				"required" => true,
                    'message' => 'Email can not be empty.'
                )
            ),
            'organization_id' => array(
                'ruleRequired'=> array(
                    'rule' => 'notBlank',
																				"required" => true,
                    'message' => 'Organization Id can not be empty.'
                )
            )
								);
}
?>