<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');
class DefaultOrg extends AppModel 
{
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
}
?>