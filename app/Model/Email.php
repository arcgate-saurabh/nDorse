<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');
class Email extends AppModel 
{
				public $validate = array(
								'to' => array(
                'ruleRequired'=> array(
                    'rule' => 'notBlank',
																				"required" => true,
                    'message' => 'Email can not be empty.'
                )
            ),
            'subject' => array(
                'ruleRequired'=> array(
                    'rule' => 'notBlank',
																				"required" => true,
                    'message' => 'Subject can not be empty.'
                )
            ),
												"config_vars" => array(
                'ruleRequired'=> array(
                    'rule' => 'notBlank',
																				"required" => true,
                    'message' => 'Config variables can not be empty.'
                )
            )
								);
}
?>