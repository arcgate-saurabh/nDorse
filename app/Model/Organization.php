<?php

App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class Organization extends AppModel {

    public $validate = array(
        'name' => array(
            'ruleRequired' => array(
                'rule' => 'notBlank',
                "required" => true,
                'message' => 'Please enter your organization name.'
            ),
            'ruleUnique' => array(
                'rule' => 'isOrgUnique',
                'required' => 'create',
                //"on" => 'update',
                'message' => 'Organization name already exists.'
            )
        ),
        //'short_name' => array(
        //    'ruleRequired' => array(
        //        'rule' => 'notBlank',
        //        "required" => true,
        //        'message' => 'Short name can not be empty.'
        //    ),
        //    'ruleUnique' => array(
        //        'rule' => 'isUnique',
        //        'required' => 'create',
        //        //"on" => 'update',
        //        'message' => 'Short name already exists.'
        //    )
        //),
        //'secret_code' => array(
        //    'ruleRequired' => array(
        //        'rule' => 'notBlank',
        //        "required" => true,
        //        'message' => 'Secret code can not be empty.'
        //    )
        //),
        //'country' => array(
        //    'ruleRequired' => array(
        //        'rule' => 'notBlank',
        //        "required" => true,
        //        'message' => 'country name can not be empty.'
        //    )
        //),
        //'state' => array(
        //    'ruleRequired' => array(
        //        'rule' => 'notBlank',
        //        "required" => true,
        //        'message' => 'state name can not be empty.'
        //    )
        //),
        //'city' => array(
        //    'ruleRequired' => array(
        //        'rule' => 'notBlank',
        //        "required" => true,
        //        'message' => 'city name can not be empty.'
        //    )
        //),
        //'street' => array(
        //    'ruleRequired' => array(
        //        'rule' => 'notBlank',
        //        "required" => true,
        //        'message' => 'street name can not be empty.'
        //    )
        //),
        //'zip' => array(
        //    'ruleRequired' => array(
        //        'rule' => 'notBlank',
        //        "required" => true,
        //        'message' => 'zip code can not be empty.'
        //    )
        //),
        'image' => array(
           'ruleValid' => array(
              'rule' => array('validateImage'),
          )
       ),
    );
    
    public function isOrgUnique($params) {
        return $this->find('count', array('conditions' => array('Organization.name' => $params['name'], "status" => array(0,1,2)))) ? false : true;
    }

}

?>