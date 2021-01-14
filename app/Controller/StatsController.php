<?php

class StatsController extends AppController {
    public $components = array("Auth", "Common", "Session");
    public $uses = array("OrgCoreValue", "Endorsement", "Organization", "Subscription", "EndorseCoreValue");
    public function beforeFilter() {
        parent::beforeFilter();
    }
    
    public function index() {
        //Total core values
//        $params = array();
//        $params['fields'] = array("COUNT(DISTINCT LOWER(name)) as total_core_values");
//        $result = $this->OrgCoreValue->find("first", $params);
//        $totalCoreValues = $result[0]['total_core_values'];
        
        $totalCoreValues = $this->EndorseCoreValue->find("count", array());
        
        //total enodresments
        $totalEndorsements = $this->Endorsement->find("count", array());
        
        //total organizations
        $orgStats = $this->Organization->find("all", array("fields" => array("count(Organization.id) AS count, status"),"group" => "status", "order" => array("status ASC")));
        
        $inActiveOrgs = $orgStats[0][0]['count'];
        $activeOrgs = $orgStats[1][0]['count'];
        $deletedOrgs = $orgStats[2][0]['count'];
        $totalOrgs = $inActiveOrgs + $activeOrgs + $deletedOrgs;
        
        //Subscriptions
        $params = array();
        $conditions = array();
        $conditions['Organization.status'] = 1;
        $params['conditions'] = $conditions;
        $params['fields'] = array("*, count(Subscription.id) AS count, type");
        $params['group'] = "type";
        $params['order'] = array("type ASC");
        
        $subscriptionStats = $this->Subscription->find("all", $params);
        
        $trialOrgs = $subscriptionStats[0][0]['count'];
        $subscribedOrgs = $subscriptionStats[1][0]['count'];
                
        //subscription
                //trial
            //inactive
        //total nDorse users
        $activeUsers = $this->UserOrganization->query("
            SELECT DISTINCT user_id from user_organizations AS UserOrganization
WHERE UserOrganization.status = 1
            ");
        
//        pr($activeUsers);die;
        $activeUsersList = array();
        
        foreach($activeUsers as $activeUser) {
            $activeUsersList[] = $activeUser['UserOrganization']['user_id'];
        }
        
        $totalActiveUsers = count($activeUsersList);
        
//        $activeUsersString = implode(",", $activeUsersList);
        
        $params = array();
        $inactiveUsers = $this->UserOrganization->find("first", array("fields" => array("COUNT(DISTINCT user_id) AS count"), "conditions" => array("UserOrganization.status" => array(0, 3), "UserOrganization.user_id NOT IN" => $activeUsersList)));
        $totalInactiveUsers = $inactiveUsers[0]['count'];
//        $totalInactiveUsers = $this->UserOrganization->query("SELECT COUNT(DISTINCT user_id) from user_organizations AS UserOrganization
//                    WHERE (UserOrganization.status = 0 OR UserOrganization.status = 3) AND UserOrganization.user_id NOT IN
//                    (" . $activeUsersString . ")"
//                        );
//        
//        pr($totalInactiveUsers);die;
        
        
        $this->set(compact('totalCoreValues', 'totalEndorsements', 'inActiveOrgs', 'activeOrgs', 'totalOrgs', 'trialOrgs', 'subscribedOrgs', 'totalActiveUsers', 'totalInactiveUsers'));
        
    }

}
