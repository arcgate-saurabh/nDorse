<?php

App::uses('Component', 'Controller');
App::uses('CakeEmail', 'Network/Email');

class OrgManagerComponent extends Component
{

    public $components = array('Session', 'Image', 'Auth', 'Apicalls');

    public function joinOrganizationData($loggedinUser = array())
    {
        $flashMsg = array();
        $redirect = array();
        $orgdata = array();
        $type = "public";
        $response = array("type" => $type);
        if (isset($loggedinUser)) {
            // $loggedinUser = $this->Auth->user();

            if (isset($loggedinUser['portal']) && $loggedinUser['portal'] == 'client') {
                $termsAccept = isset($loggedinUser['terms_accept']) ? $loggedinUser['terms_accept'] : 0;
                if (!$termsAccept) {
                    $flashMsg[] = __('Accept End User License Agreement');
                    $flashMsg[] = "default";
                    $flashMsg[] = array('class' => 'alert alert-warning');
                    // $this->Session->setFlash(__('Accept End User License Agreement'), 'default', array('class' => 'alert alert-warning'));
                    $redirect = array('controller' => 'client', 'action' => 'setOrg');
                    $response["redirect"] = $redirect;
                    $response["flash_msg"] = $flashMsg;
                    return $response;
                }

                $type = $response["type"];
                $postdata = array("token" => $loggedinUser["token"], "type" => $type, "limit" => 15);
                $jsondata = $this->Apicalls->curlpost("getAllOrganization.json", $postdata);
                $jsondatadecoded = json_decode($jsondata, true);
                //$orgdata = isset($jsondatadecoded["result"]["data"]) ? $jsondatadecoded["result"]["data"] : $jsondatadecoded["result"]["msg"];
                if (isset($jsondatadecoded["result"]["data"])) {
                    $orgdata = $jsondatadecoded["result"]["data"];
                    $response["orgdata"] = $orgdata;
                } else {
                    // $this->Session->setFlash(__($jsondatadecoded["result"]["msg"]), 'default', array('class' => 'alert alert-warning'));
                    $flashMsg = array(__($jsondatadecoded["result"]["msg"]), 'default', array('class' => 'alert alert-warning'));
                    $redirect = $this->Auth->logout();
                    $response["redirect"] = $redirect;
                    $response["flash_msg"] = $flashMsg;
                    // $this->redirect($this->Auth->logout());
                }
            }
        } else {
            $response["redirect"] = array('controller' => 'client', 'action' => 'login');
        }
        return $response;
    }

    public function userOrganizations($loggedinUser, $type)
    {
        $postdata = array("token" => $loggedinUser["token"], "type" => $type, "limit" => 15);
        $jsondata = $this->Apicalls->curlpost("getAllOrganization.json", $postdata);
        //                pr($jsondata); exit;
        $jsondatadecoded = json_decode($jsondata, true);
        return $jsondatadecoded;
    }
}
