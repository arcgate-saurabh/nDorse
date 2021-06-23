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

    /**
     * Returns the dictionary of Endorsements of given Organization ID in current month
     * for each Sub Center
     * @param @org
     * 
     * */
    public function subcenterCurrentMonthEndorsementsDict($organization_id=0){
        $data = array();
        $criteria = array();
        $day1_month_date = date("Y-m-01");
        $criteria['conditions'] = array("Endorsement.organization_id" => $organization_id, "Endorsement.created >" => $day1_month_date);
        $criteria['fields'] = array("COUNT(Endorsement.id) as ct", "subcenter_for");
        $criteria['group'] = array('subcenter_for');
        $result = $this->find("all", $criteria);
        if (isset($result)){
            foreach( $result as $i=>$row){
                $data[$row["Endorsement"]["subcenter_for"]] = $row[0]["ct"];
            }
        }

        return $data;
    }

}

?>
