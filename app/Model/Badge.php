<?php

App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class Badge extends AppModel {

    public $belongsTo = array(
        'Trophy' => array(
            'className' => 'Trophy',
            'foreignKey' => 'trophy_id'
        )
    );
}
?>