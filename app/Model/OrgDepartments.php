<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');
class OrgDepartments extends AppModel 
{
				public $belongsTo = array(
        'Organization' => array(
            'className' => 'Organization',
            'foreignKey' => 'organization_id'
        )
    );
}
?>