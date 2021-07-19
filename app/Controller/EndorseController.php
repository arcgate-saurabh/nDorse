<?php

class EndorseController extends AppController {

    public $components = array('RequestHandler', "Auth", "Common", "Session", "Apicalls", 'VideoEncoder');

    public function beforeFilter() {

        parent::beforeFilter();
        $this->layout = "clientlayout";
        $this->Auth->deny();
        $loggedinUser = $this->Auth->user();

        //pr($loggedinUser); exit;

        if (isset($loggedinUser['corepassword'])) {
            $postData['email'] = $loggedinUser['email'];
            $postData['password'] = $loggedinUser['corepassword'];
//            pr($postData); exit;
            $response = $this->Apicalls->curlpost("login.json", $postData);
//            pr($response); exit;
            $response = json_decode($response);
            $response = $response->result;
//                      pr($response); exit;
            if ($response->status == 1) {
                $userData = (array) $response->data;

                $userData['portal'] = 'client';

//                pr($userData);
//                exit;
                if (isset($userData['org_updates'])) {
                    $userData['org_updates'] = (array) $userData['org_updates'];
                }
//                pr($userData);die;

                $this->Session->write('Auth.User', $userData);

                $loggedinUser = $this->Auth->user();

//                pr($loggedinUser);
//                exit;
            }
        }
//        exit;

        if (isset($loggedinUser['org_updates']) && ($loggedinUser['org_updates']['org_status'] != 'active' || $loggedinUser['org_updates']['user_status'] != "active")) {
            $this->redirect(array('controller' => 'client', 'action' => 'inactiveOrg'));
        }
    }

    public function beforeRender() {
        $loggedinUser = $this->Auth->user();
//        pr($loggedinUser); exit;
        if ($loggedinUser['current_org']->joined == 0) {
            $currentOrg = $loggedinUser['current_org'];
            $currentOrg->joined = 1;
            $this->Session->write('Auth.User.current_org', $currentOrg);
        }
    }

    public function videoupload() {
        $this->layout = 'ajax';
        $this->autoRender = false;
        $loggedinUser = $this->Auth->user();
        $currentOrg = $loggedinUser['current_org'];
        $user_id = $loggedinUser["id"];
        $uploadPath = VIDEO_DIR;

        if (empty($_FILES)) {
            echo $result = json_encode(array(
        'result' => array("status" => false
            , "msg" => "File is empty"),
        '_serialize' => array('result')
            ));
            exit;
        }
        $fileName_temp = str_replace(" ", "_", $_FILES['file']['name']);

        $fileName = date('dmy_His') . '_' . $fileName_temp;
        $fileNameCompressed = date('dmy_His') . '_compressed_' . $fileName_temp;


        $this->loadModel('OrgVideo');
        $orgVideoLimit = $currentOrg->featured_video_limit;
        $orgFirstActiveVideo = $this->OrgVideo->find('list', array('fields' => array('id'), 'conditions' => array('org_id' => $currentOrg->id, 'status' => 1), 'order' => array('created asc'), 'limit' => $orgVideoLimit));
        if (!empty($orgFirstActiveVideo)) {
            $uploadedVideos = count($orgFirstActiveVideo);
            if ($uploadedVideos >= $orgVideoLimit) {
                $prvActiveVideoID = array_shift($orgFirstActiveVideo);
                $this->OrgVideo->id = $prvActiveVideoID;
                $this->OrgVideo->save(array('status' => 3));
            }
        }

        $full_video_path = $uploadPath . $fileName;
        $full_video_path_compressed = $uploadPath . $fileNameCompressed;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $full_video_path)) {
            chmod($full_video_path, 0777);

            /* Get video thumbnail */
            $alldetailsorg = $this->Common->saveVideoImageData($full_video_path, $interval = "00:00:02", $size_array = array('width' => 200, 'height' => 200));

            //Router::url('/', true) . "app/webroot/" . 
//            $fullvideoPath = $full_video_path_compressed;
//            $cmd1 = "ffmpeg -i $fullvideoPath  -vf setsar=1 $fullvideoPath";
//            $fullvideoPath = $full_video_path_compressed;
            $cmd2 = "ffmpeg -i $full_video_path -vcodec libx264 $full_video_path_compressed";
//                echo $cmd1;
//                exit;
//            $output_including_status = shell_exec($cmd1);
            $output_including_status_2 = shell_exec($cmd2);
//            echo "output_including_status".$output_including_status;
//            echo "<br/>output_including_status".$output_including_status_2;
////            echo "cmd1".$cmd1;
//            echo "<br/>cmd2 ".$cmd2;
//            exit;
//            chmod($full_video_path_compressed, 0777);
            $videoThubnail = '';

            if (!empty($alldetailsorg)) {
                if ($alldetailsorg['error'] == 'no') {
                    $videoThubnail = $alldetailsorg['thumb'];
                }
            }

            $Video['OrgVideo']['thumbnail'] = $videoThubnail;
            $Video['OrgVideo']['video_url'] = $fileNameCompressed;
            $Video['OrgVideo']['org_id'] = $currentOrg->id;
            $Video['OrgVideo']['uploaded_by'] = $user_id;
            $this->OrgVideo->create();
            if ($this->OrgVideo->save($Video)) {
                $lastInserID = $this->OrgVideo->id;
                $full_video_path = Router::url('/', true) . "app/webroot/" . $full_video_path;
                echo $result = json_encode(array('result' => array("status" => true, "filepath" => $full_video_path, "videoId" => $lastInserID, "msg" => $full_video_path, "Uploaded"), '_serialize' => array('result')));
            } else {
                echo $result = json_encode(array(
            'result' => array("status" => false
                , "msg" => "Unable to upload"),
            '_serialize' => array('result')
                ));
            }
            //$jsondata = $this->Apicalls->curlpost("endorsestats.json", $postdata);
            $flagError = false;
        } else {
            $errorMessage = 'There was a problem uploading file. Please try again.';
        }
    }

    public function ADFSLiveFeed() {
        $this->autoRender = false;
        $this->layout = false;
        //Check only for ADFS login user   
        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('Auth.User')) {
            if (isset($loggedinUser['current_org'])) {
                if ($loggedinUser['source'] == 'ADFS') {
                    $postdata['token'] = $loggedinUser["token"];
                    $postdata['sso_url'] = 'https://sso.ndorse.net/simplesaml/module.php/core/authenticate.php?as=ndorse-sp';
                    $jsondata = $this->Apicalls->curlpost("checkADFSLoginSession.json", $postdata);
                    pr($jsondata);
                } else {
                    $this->redirect(array('controller' => 'endorse/'));
                }
                exit;
            } else {
                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }
    }

    public function index() {
        $loggedinUser = $this->Auth->user();
        //pr($loggedinUser);
        //exit;
        if ($this->Session->check('Auth.User')) {
            if (isset($loggedinUser['current_org'])) {
                $orgName = $loggedinUser['current_org']->name;
                $user_subcenterID = 0;
//                if (isset($loggedinUser['current_org']->subcenter_id)) {
//                    $user_subcenterID = $loggedinUser['current_org']->subcenter_id;
//                }


                $postdata = array("token" => $loggedinUser["token"], "type" => "public", "subcenter_id" => $user_subcenterID);
//                
//                $postdata = array("token" => $loggedinUser["token"], "user_id" => 1363);
//                    $jsondata = $this->Apicalls->curlpost("UnfollowUser.json", $postdata);
//                $postdata = array("token" => $loggedinUser["token"],'type' => 'following');
//                                pr(json_encode($postdata)); exit;
//                    $jsondata = $this->Apicalls->curlget("getUserFollowList.json", $postdata);
                //                //$jsondata = $this->Apicalls->curlpost("getEndorseList.json", $postdata);
//                $jsondata = $this->Apicalls->curlpost("getAllLast15Notifications.json", $postdata);
//                $postdata = array("token" => $loggedinUser["token"], "status" => 1);
//                    $jsondata = $this->Apicalls->curlpost("getPendingGuestnDorsements.json", $postdata);
//                $postdata = array("token" => $loggedinUser["token"], "status" => 2, 'endorsement_id' => 12351);
//                $jsondata = $this->Apicalls->curlpost("changeGuestEndorsementStatus.json", $postdata);
//                //              
//                                $jsondata = $this->Apicalls->curlpost("getActiveUserList.json", $postdata);  
//                    pr($jsondata);
//                    exit;



                $jsondata = $this->Apicalls->curlpost("getLiveFeeds.json", $postdata);
                //pr($jsondata); exit;
                $jsondatadecoded = json_decode($jsondata, true);
                $org_id = $loggedinUser['current_org']->id;
                $postdata = array("token" => $loggedinUser["token"], "org_id" => $org_id);
                $videojsondata = $this->Apicalls->curlpost("getFeaturedVideoList.json", $postdata);
//                pr($videojsondata); exit;
                $videojsondecodedata = json_decode($videojsondata, true);
                $featured_video_enabled = 0;
                if (isset($videojsondecodedata['result']['data']['featured_video_enabled'])) {
                    $featured_video_enabled = $videojsondecodedata['result']['data']['featured_video_enabled'];
                }
//                echo $featured_video_enabled;exit;
//                
//                $alldetailsorg = $this->Common->OrgInfoClient($loggedinUser["token"], $org_id);
//                
//                
//                if (!empty($alldetailsorg)) {
//                    $featured_video_enabled = $alldetailsorg['featured_video_enabled'];
//                }
//                exit;
//                

                $orgVideoList = $videojsondecodedata['result']['data']['org_video_list'];
//                pr($postdata);
                $subcenterData = array();
                $SCjsondata = $this->Apicalls->curlpost("getOrgSubcenters.json", $postdata);
                //pr($SCjsondata); exit;
                if (isset($SCjsondata) && $SCjsondata != '') {
                    $subcenterArray = json_decode($SCjsondata, true);
                    if (isset($subcenterArray['result']['data'])) {
                        $subcenterData = $subcenterArray['result']['data'];
                    }
                }
                
                
                $user_id = $loggedinUser["id"];
                $user_role = $loggedinUser["role"];
                $org_user_role = $loggedinUser['current_org']->org_role;

                /* Added by Babulal prasad at @11MAY2021
                 * if filter is following then get following users list and get the feed
                 */
                $this->loadModel("UserFollowing");
                $followingIdsArray = array();
                $userFollowings = $this->UserFollowing->find('all', array('fields' => array('*'), 'conditions' => array('user_id' => $user_id, 'status' => 1)));
                if (!empty($userFollowings)) {
                    $userFollowings = array_shift($userFollowings);
                    $userFollowings = $userFollowings['UserFollowing'];
                    $uFollowingID = $userFollowings['id'];
                    $followingIdsArray = json_decode($userFollowings['following_ids']);
                    $followingIdsArray = array_filter($followingIdsArray); // Removing empty value/ids
                }
                $this->set('followingIdsArray', $followingIdsArray);




                


                $user_do_not_remind = $loggedinUser["do_not_remind"];

                if ($jsondatadecoded["result"]["status"]) {
                    $endorsedatadata = $jsondatadecoded["result"]["data"];
                    $this->set('endorsedata', $endorsedatadata["endorse_data"]);
                    $this->set('endorsepage', $endorsedatadata["total_page"]);
                    $this->set('servertime', $endorsedatadata["server_time"]);
                    $this->set('logged_user_id', $user_id);
                    $this->set('user_role', $user_role);
                    $this->set('org_user_role', $org_user_role);
                    $this->set('user_do_not_remind', $user_do_not_remind);
                    $this->set('featured_video_enabled', $featured_video_enabled);
                    $this->set('orgVideoList', $orgVideoList);
                    $this->set(compact('subcenterData', 'user_subcenterID', 'orgName'));
                } else {
                    $errormsg = $jsondatadecoded["result"]["msg"];
                    $this->Session->write('error', $errormsg);
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }
            } else {

                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }




        $this->set('jsIncludes', array('endorse', 'editEndorsementMessage'));
        $this->set('addEndorse', true);
        $this->set('MenuName', 'Live Feed');
    }

    public function likeslist() {
        $this->layout = false;
        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('Auth.User')) {
            $feedid = $this->data['id'];
            $feedtype = $this->data['type'];
            $page = $this->data['pg'];
            $postData = array("token" => $loggedinUser["token"], "type" => $feedtype, "id" => $feedid, 'page' => $page);
            $response = $this->Apicalls->curlpost("getLikesList.json", $postData);
            $jsondatadecoded = json_decode($response, true);
            $likesList = $jsondatadecoded['result']['data']['likes_list'];
//            if(!empty($jsondatadecoded['result']['data']['likes_list'])){
//                $likesList = $jsondatadecoded['result']['data']['likes_list'];
//            }else{
//                exit;
//            }
            $this->set('likesList', $likesList);
        }
    }

    public function index_test() {
        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('Auth.User')) {
            if (isset($loggedinUser['current_org'])) {
                $postdata = array("token" => $loggedinUser["token"], "type" => "public");
                //$jsondata = $this->Apicalls->curlpost("getEndorseList.json", $postdata);
//                $jsondata = $this->Apicalls->curlpost("getLiveFeeds.json", $postdata);
                $jsondata = $this->Apicalls->curlpost("getLiveFeeds2.json", $postdata);
//                pr($jsondata); exit;
                $jsondatadecoded = json_decode($jsondata, true);

                $user_id = $loggedinUser["id"];
                $user_role = $loggedinUser["role"];
                $user_do_not_remind = $loggedinUser["do_not_remind"];

                if ($jsondatadecoded["result"]["status"]) {
                    $endorsedatadata = $jsondatadecoded["result"]["data"];
                    $this->set('endorsedata', $endorsedatadata["endorse_data"]);
                    $this->set('endorsepage', $endorsedatadata["total_page"]);
                    $this->set('servertime', $endorsedatadata["server_time"]);
                    $this->set('logged_user_id', $user_id);
                    $this->set('user_role', $user_role);
                    $this->set('user_do_not_remind', $user_do_not_remind);
                } else {
                    $errormsg = $jsondatadecoded["result"]["msg"];
                    $this->Session->write('error', $errormsg);
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }
            } else {

                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }

        $this->set('jsIncludes', array('endorse'));
        $this->set('addEndorse', true);
        $this->set('MenuName', 'Live Feed');
    }

    public function ndorse() {
        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('Auth.User')) {

            if (isset($loggedinUser['current_org'])) {
                $postdata = array("token" => $loggedinUser["token"], "type" => "endorser");
                $jsondata = $this->Apicalls->curlpost("getEndorseList.json", $postdata);

                $jsondatadecoded = json_decode($jsondata, true);
//                pr($jsondatadecoded); exit;
                if ($jsondatadecoded["result"]["status"]) {
                    $endorsedatadata = $jsondatadecoded["result"]["data"];
                    $this->set('endorsedata', $endorsedatadata["endorse_data"]);
                    $this->set('endorsepage', $endorsedatadata["total_page"]);
                    $this->set('servertime', $endorsedatadata["server_time"]);
                } else {
                    $errormsg = $jsondatadecoded["result"]["msg"];
                    $this->Session->write('error', $errormsg);
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }
            } else {
                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }
        $org_user_role = $loggedinUser['current_org']->org_role;
        $logged_user_id = $loggedinUser['id'];
        $this->set('jsIncludes', array('endorse'));
        $this->set('MenuName', 'nDorser');
        $this->set('addEndorse', true);
        $this->set('org_user_role', $org_user_role);
        $this->set('logged_user_id', $logged_user_id);
    }

    public function ndorsed() {
        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('Auth.User')) {

            if (isset($loggedinUser['current_org'])) {
                $postdata = array("token" => $loggedinUser["token"], "type" => "endorsed");
                $jsondata = $this->Apicalls->curlpost("getEndorseList.json", $postdata);
                $jsondatadecoded = json_decode($jsondata, true);
                if ($jsondatadecoded["result"]["status"]) {
                    $endorsedatadata = $jsondatadecoded["result"]["data"];
                    $this->set('endorsedata', $endorsedatadata["endorse_data"]);
                    $this->set('endorsepage', $endorsedatadata["total_page"]);
                    $this->set('servertime', $endorsedatadata["server_time"]);
                } else {
                    $errormsg = $jsondatadecoded["result"]["msg"];
                    $this->Session->write('error', $errormsg);
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }
            } else {
                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }

        $this->set('jsIncludes', array('endorse'));
        $this->set('addEndorse', true);
        $this->set('MenuName', 'nDorsed');
    }

    public function details($id) {

        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('Auth.User')) {

            if (isset($loggedinUser['current_org'])) {
                $postdata = array("token" => $loggedinUser["token"], "e_id" => $id);
                $jsondata = $this->Apicalls->curlpost("endorsedetails.json", $postdata);
                $jsonCommentdata = $this->Apicalls->curlpost("getEndorseCommentsLists.json", $postdata);
                $jsonCommentdatadecoded = json_decode($jsonCommentdata, true);
//                pr($jsonCommentdata); exit;

                $jsondatadecoded = json_decode($jsondata, true);
                //pr($jsondata); exit;

                $Orgresponse = $this->Organization->findById($loggedinUser['current_org']->id);

                if (!empty($Orgresponse)) {
                    $this->set('optionalComments', $Orgresponse['Organization']['optional_comments']);
                    $this->set('endorseMessageMinLimit', $Orgresponse['Organization']['endorse_message_min_limit']);
                } else {
                    $this->set('optionalComments', $loggedinUser['current_org']->optional_comments);
                    $this->set('endorseMessageMinLimit', $loggedinUser['current_org']->endorse_message_min_limit);
                }
            
                
                if ($jsondatadecoded["result"]["status"]) {
                    $endorsedatadata = $jsondatadecoded["result"]["data"];

                    $user_id = $loggedinUser["id"];
                    $this->set('endorsedata', $endorsedatadata);
                    $this->set('endorseCommentData', $jsonCommentdatadecoded);
                    $this->set('logged_user_id', $user_id);
                    $this->set('loggeduserimage', $loggedinUser['image']);
                    $this->set('optionalComments', $loggedinUser['current_org']->optional_comments);
                    $this->set('endorseMessageMinLimit', $loggedinUser['current_org']->endorse_message_min_limit);
//                    pr($endorsedatadata);exit;
                } else {
                    $errormsg = $jsondatadecoded["result"]["msg"];
                    $this->Session->write('error', $errormsg);
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }
            } else {
                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }

        $this->set('jsIncludes', array('endorse_details', 'editEndorsementMessage'));
        $this->set('addEndorse', true);
        $this->set('MenuName', 'nDorsement Detail');
    }

    // endorse stats
    public function stats() {
        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('Auth.User')) {

            if (isset($loggedinUser['current_org'])) {

                $postdata = array("token" => $loggedinUser["token"]);
                $jsondata = $this->Apicalls->curlpost("endorsestats.json", $postdata);
                $jsondatadecoded = json_decode($jsondata, true);

                if ($jsondatadecoded["result"]["status"]) {
                    $endorsedatadata = $jsondatadecoded["result"]["data"];
                    $this->set('statesdata', $endorsedatadata);
                    $this->set('profiledata', $loggedinUser);
                } else {
                    $errormsg = $jsondatadecoded["result"]["msg"];
                    $this->Session->write('error', $errormsg);
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }
            } else {
                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }

        $this->set('jsIncludes', array('endorse_stats'));
        $this->set('addEndorse', true);

        $this->set('MenuName', 'nDorsements');
    }

    public function charts() {
        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('Auth.User')) {

            if (isset($loggedinUser['current_org'])) {

                $postdata = array("token" => $loggedinUser["token"]);
                $jsondata = $this->Apicalls->curlpost("endorsestats.json", $postdata);
                $jsondatadecoded = json_decode($jsondata, true);

                if ($jsondatadecoded["result"]["status"]) {
                    $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1, "type" => "user");
                    $startdate = "";
                    $enddate = "";

                    if (1) {

                        $enddate = $startdate = "";
                        if (isset($this->request->data["daterange"]["startdate"]) && $this->request->data["daterange"]["startdate"] != "") {
                            $startdate = $this->request->data["daterange"]["startdate"];
                        }
                        if (isset($this->request->data["daterange"]["enddate"]) && $this->request->data["daterange"]["enddate"] != "") {
                            $enddate = $this->request->data["daterange"]["enddate"];
                        }

                        if ($startdate != "" && $enddate != "") {
                            $startdatenew = explode("-", $this->request->data["daterange"]["startdate"]);
                            $enddatenew = explode("-", $this->request->data["daterange"]["enddate"]);
                            $startdatenew = mktime(0, 0, 0, $startdatenew[0], $startdatenew[1], $startdatenew[2]);
                            $enddatenew = mktime(0, 0, 0, $enddatenew[0], $enddatenew[1], $enddatenew[2]);
                            $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1, "start_date" => $startdatenew, "end_date" => $enddatenew, "type" => "user");
                        } elseif ($startdate != "") {
                            $startdatenew = explode("-", $this->request->data["daterange"]["startdate"]);

                            $startdatenew = mktime(0, 0, 0, $startdatenew[0], $startdatenew[1], $startdatenew[2]);

                            $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1, "start_date" => $startdatenew, "type" => "user");
                        } else {
                            $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1, "type" => "user");
                        }
                    }
                    $org_id = $loggedinUser["current_org"]->id;
                    $alldetailsorg = $this->Common->OrgInfoClient($loggedinUser["token"], $org_id);
                    $jsondataforgraph = json_decode($this->Apicalls->curlpost("endorsementbycorevalues.json", $postdataforgraph), true);

                    $graphbycorevalues = "";
                    if ($jsondataforgraph["result"]["status"] == 1) {
                        $graphbycorevalues = $jsondataforgraph["result"]["data"];

                        $this->set('jsIncludes', array('endorse_charts'));
                        $this->set('graphbycorevalues', $graphbycorevalues);
                    } else {
                        $graphbycorevalues = "";

                        $this->set('jsIncludes', array('endorse_charts'));
                        $this->set('graphbycorevalues', $graphbycorevalues);
                    }
                    $this->set('alldetailsorg', $alldetailsorg);
                } else {
                    $errormsg = $jsondatadecoded["result"]["msg"];
                    $this->Session->write('error', $errormsg);
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }
            } else {
                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }

        $this->set('jsIncludes', array('endorse_charts'));
        $this->set('addEndorse', true);
        $this->set('MenuName', 'nDorsement By Core Values');
    }

// nDorsement by Department
    public function departments() {
        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('Auth.User')) {

            if (isset($loggedinUser['current_org'])) {

                $postdata = array("token" => $loggedinUser["token"]);
                $jsondata = $this->Apicalls->curlpost("endorsestats.json", $postdata);
                //pr($jsondata); exit;
                $jsondatadecoded = json_decode($jsondata, true);

                if ($jsondatadecoded["result"]["status"]) {
                    $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1);
                    $startdate = "";
                    $enddate = "";

                    if (1) {

                        $enddate = $startdate = "";
                        if (isset($this->request->data["daterange"]["startdate"]) && $this->request->data["daterange"]["startdate"] != "") {
                            $startdate = $this->request->data["daterange"]["startdate"];
                        }
                        if (isset($this->request->data["daterange"]["enddate"]) && $this->request->data["daterange"]["enddate"] != "") {
                            $enddate = $this->request->data["daterange"]["enddate"];
                        }

                        if ($startdate != "" && $enddate != "") {
                            $startdatenew = explode("-", $this->request->data["daterange"]["startdate"]);
                            $enddatenew = explode("-", $this->request->data["daterange"]["enddate"]);
                            $startdatenew = mktime(0, 0, 0, $startdatenew[0], $startdatenew[1], $startdatenew[2]);
                            $enddatenew = mktime(0, 0, 0, $enddatenew[0], $enddatenew[1], $enddatenew[2]);
                            $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1, "start_date" => $startdatenew, "end_date" => $enddatenew);
                        } elseif ($startdate != "") {
                            $startdatenew = explode("-", $this->request->data["daterange"]["startdate"]);

                            $startdatenew = mktime(0, 0, 0, $startdatenew[0], $startdatenew[1], $startdatenew[2]);

                            $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1, "start_date" => $startdatenew);
                        } else {
                            $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1);
                        }
                    }
//                    echo $test = $this->Apicalls->curlpost("endorsementbydept.json", $postdataforgraph); exit;
                    $jsondataforgraph = json_decode($this->Apicalls->curlpost("endorsementbydept.json", $postdataforgraph), true);
                    $jsondataforgraph = json_decode($this->Apicalls->curlpost("endorsementbydept.json", $postdataforgraph), true);
//                    pr($jsondataforgraph); exit;
                    $graphbycorevalues = "";
                    $org_id = $loggedinUser["current_org"]->id;
                    $alldetailsorg = $this->Common->OrgInfoClient($loggedinUser["token"], $org_id);
                    if ($jsondataforgraph["result"]["status"] == 1) {
                        $graphbycorevalues = $jsondataforgraph["result"]["data"];

                        $this->set('jsIncludes', array('endorse_charts'));
                        $this->set('graphbycorevalues', $graphbycorevalues);
                    } else {
                        $graphbycorevalues = "";

                        $this->set('jsIncludes', array('endorse_charts'));
                        $this->set('graphbycorevalues', $graphbycorevalues);
                    }
                    $this->set('alldetailsorg', $alldetailsorg);
                } else {
                    $errormsg = $jsondatadecoded["result"]["msg"];
                    $this->Session->write('error', $errormsg);
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }
            } else {
                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }

        $this->set('jsIncludes', array('endorse_charts'));
        $this->set('addEndorse', true);
        $this->set('MenuName', 'nDorsement History By Department');
    }

//
// nDorsement by job title
    public function jobtitle() {
        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('Auth.User')) {

            if (isset($loggedinUser['current_org'])) {

                $postdata = array("token" => $loggedinUser["token"]);
                $jsondata = $this->Apicalls->curlpost("endorsestats.json", $postdata);
                $jsondatadecoded = json_decode($jsondata, true);

                if ($jsondatadecoded["result"]["status"]) {
                    $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1);
                    $startdate = "";
                    $enddate = "";

                    if (1) {

                        $enddate = $startdate = "";
                        if (isset($this->request->data["daterange"]["startdate"]) && $this->request->data["daterange"]["startdate"] != "") {
                            $startdate = $this->request->data["daterange"]["startdate"];
                        }
                        if (isset($this->request->data["daterange"]["enddate"]) && $this->request->data["daterange"]["enddate"] != "") {
                            $enddate = $this->request->data["daterange"]["enddate"];
                        }

                        if ($startdate != "" && $enddate != "") {
                            $startdatenew = explode("-", $this->request->data["daterange"]["startdate"]);
                            $enddatenew = explode("-", $this->request->data["daterange"]["enddate"]);
                            $startdatenew = mktime(0, 0, 0, $startdatenew[0], $startdatenew[1], $startdatenew[2]);
                            $enddatenew = mktime(0, 0, 0, $enddatenew[0], $enddatenew[1], $enddatenew[2]);
                            $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1, "start_date" => $startdatenew, "end_date" => $enddatenew);
                        } elseif ($startdate != "") {
                            $startdatenew = explode("-", $this->request->data["daterange"]["startdate"]);

                            $startdatenew = mktime(0, 0, 0, $startdatenew[0], $startdatenew[1], $startdatenew[2]);

                            $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1, "start_date" => $startdatenew);
                        } else {
                            $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1);
                        }
                    }
                    //  print_r($postdataforgraph);exit;

                    $jsondataforgraph = json_decode($this->Apicalls->curlpost("endorsementbyjobtitles.json", $postdataforgraph), true);
                    $org_id = $loggedinUser["current_org"]->id;
                    $alldetailsorg = $this->Common->OrgInfoClient($loggedinUser["token"], $org_id);

                    $graphbycorevalues = "";
                    if ($jsondataforgraph["result"]["status"] == 1) {
                        $graphbycorevalues = $jsondataforgraph["result"]["data"];

                        $this->set('jsIncludes', array('endorse_charts'));
                        $this->set('graphbycorevalues', $graphbycorevalues);
                    } else {
                        $graphbycorevalues = "";

                        $this->set('jsIncludes', array('endorse_charts'));
                        $this->set('graphbycorevalues', $graphbycorevalues);
                    }
                    $this->set('alldetailsorg', $alldetailsorg);
                } else {
                    $errormsg = $jsondatadecoded["result"]["msg"];
                    $this->Session->write('error', $errormsg);
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }
            } else {
                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }

        $this->set('jsIncludes', array('endorse_charts'));
        $this->set('addEndorse', true);
        $this->set('MenuName', 'nDorsement History By Job Title');
    }

//
// nDorsement by entity
    public function entity() {
        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('Auth.User')) {

            if (isset($loggedinUser['current_org'])) {

                $postdata = array("token" => $loggedinUser["token"]);
                $jsondata = $this->Apicalls->curlpost("endorsestats.json", $postdata);
                $jsondatadecoded = json_decode($jsondata, true);

                if ($jsondatadecoded["result"]["status"]) {
                    $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1);
                    $startdate = "";
                    $enddate = "";

                    if (1) {

                        $enddate = $startdate = "";
                        if (isset($this->request->data["daterange"]["startdate"]) && $this->request->data["daterange"]["startdate"] != "") {
                            $startdate = $this->request->data["daterange"]["startdate"];
                        }
                        if (isset($this->request->data["daterange"]["enddate"]) && $this->request->data["daterange"]["enddate"] != "") {
                            $enddate = $this->request->data["daterange"]["enddate"];
                        }

                        if ($startdate != "" && $enddate != "") {
                            $startdatenew = explode("-", $this->request->data["daterange"]["startdate"]);
                            $enddatenew = explode("-", $this->request->data["daterange"]["enddate"]);
                            $startdatenew = mktime(0, 0, 0, $startdatenew[0], $startdatenew[1], $startdatenew[2]);
                            $enddatenew = mktime(0, 0, 0, $enddatenew[0], $enddatenew[1], $enddatenew[2]);
                            $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1, "start_date" => $startdatenew, "end_date" => $enddatenew);
                        } elseif ($startdate != "") {
                            $startdatenew = explode("-", $this->request->data["daterange"]["startdate"]);

                            $startdatenew = mktime(0, 0, 0, $startdatenew[0], $startdatenew[1], $startdatenew[2]);

                            $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1, "start_date" => $startdatenew);
                        } else {
                            $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1);
                        }
                    }
                    //  print_r($postdataforgraph);exit;

                    $jsondataforgraph = json_decode($this->Apicalls->curlpost("endorsementbyentity.json", $postdataforgraph), true);

                    $graphbycorevalues = "";
                    $org_id = $loggedinUser["current_org"]->id;
                    $alldetailsorg = $this->Common->OrgInfoClient($loggedinUser["token"], $org_id);
                    if ($jsondataforgraph["result"]["status"] == 1) {
                        $graphbycorevalues = $jsondataforgraph["result"]["data"];

                        $this->set('jsIncludes', array('endorse_charts'));
                        $this->set('graphbycorevalues', $graphbycorevalues);
                    } else {
                        $graphbycorevalues = "";

                        $this->set('jsIncludes', array('endorse_charts'));
                        $this->set('graphbycorevalues', $graphbycorevalues);
                    }
                    $this->set('alldetailsorg', $alldetailsorg);
                } else {
                    $errormsg = $jsondatadecoded["result"]["msg"];
                    $this->Session->write('error', $errormsg);
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }
            } else {
                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }

        $this->set('jsIncludes', array('endorse_charts'));
        $this->set('addEndorse', true);
        $this->set('MenuName', 'nDorsement History By Sub Org');
    }

//
// nDorsement by entity
    public function day() {
        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('Auth.User')) {

            if (isset($loggedinUser['current_org'])) {

                $postdata = array("token" => $loggedinUser["token"]);
                $jsondata = $this->Apicalls->curlpost("endorsestats.json", $postdata);
                $jsondatadecoded = json_decode($jsondata, true);

                if ($jsondatadecoded["result"]["status"]) {
                    $org_id = $loggedinUser["current_org"]->id;
                    $alldetailsorg = $this->Common->OrgInfoClient($loggedinUser["token"], $org_id);
                    $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1);
                    $startdate = "";
                    $enddate = "";

                    if (1) {

                        $enddate = $startdate = "";
                        if (isset($this->request->data["daterange"]["startdate"]) && $this->request->data["daterange"]["startdate"] != "") {
                            $startdate = $this->request->data["daterange"]["startdate"];
                        }
                        if (isset($this->request->data["daterange"]["enddate"]) && $this->request->data["daterange"]["enddate"] != "") {
                            $enddate = $this->request->data["daterange"]["enddate"];
                        }

                        if ($startdate != "" && $enddate != "") {
                            $startdatenew = explode("-", $this->request->data["daterange"]["startdate"]);
                            $enddatenew = explode("-", $this->request->data["daterange"]["enddate"]);
                            $startdatenew = mktime(0, 0, 0, $startdatenew[0], $startdatenew[1], $startdatenew[2]);
                            $enddatenew = mktime(0, 0, 0, $enddatenew[0], $enddatenew[1], $enddatenew[2]);
                            $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1, "start_date" => $startdatenew, "end_date" => $enddatenew);
                        } elseif ($startdate != "") {
                            $startdatenew = explode("-", $this->request->data["daterange"]["startdate"]);

                            $startdatenew = mktime(0, 0, 0, $startdatenew[0], $startdatenew[1], $startdatenew[2]);

                            $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1, "start_date" => $startdatenew);
                        } else {
                            $postdataforgraph = array("token" => $loggedinUser["token"], "web" => 1);
                        }
                    }
                    //  print_r($postdataforgraph);exit;

                    $jsondataforgraph = json_decode($this->Apicalls->curlpost("endorsementbyday.json", $postdataforgraph), true);

                    $graphbycorevalues = "";
                    if ($jsondataforgraph["result"]["status"] == 1) {
                        $graphbycorevalues = $jsondataforgraph["result"]["data"];

                        $this->set('jsIncludes', array('endorse_charts'));
                        $this->set('graphbycorevalues', $graphbycorevalues);
                        $this->set('alldetailsorg', $alldetailsorg);
                    } else {
                        $graphbycorevalues = "";

                        $this->set('jsIncludes', array('endorse_charts'));
                        $this->set('graphbycorevalues', $graphbycorevalues);
                        $this->set('alldetailsorg', $alldetailsorg);
                        $this->set('addEndorse', true);
                    }
                } else {
                    $errormsg = $jsondatadecoded["result"]["msg"];
                    $this->Session->write('error', $errormsg);
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }
            } else {
                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }

        $this->set('jsIncludes', array('endorse_charts'));
        $this->set('addEndorse', true);
        $this->set('MenuName', 'nDorsement History By Day');
    }

//
    /* public function add() {
      //        pr($this->request->data);die;
      $this->set('MenuName', 'nDorse Now');
      $loggedinUser = $this->Auth->user();

      if ($this->Session->check('Auth.User')) {
      if (isset($loggedinUser['current_org'])) {
      if ($this->request->is('post')) {
      //                    $this->request->data['type'] = 'standard';//remove this and above 1 condition
      $this->set('jsIncludes',array('addEndorse'));

      if (!isset($this->request->data['endorsee']) && isset($this->request->data['type'])) {
      //get core values
      $postData = array("token" => $loggedinUser["token"], 'org_id' => $loggedinUser['current_org']->id);
      $response = $this->Apicalls->curlpost("getVariousOrganizationData.json", $postData);
      $response = json_decode($response);
      $response = $response->result;
      $this->set('coreValues', $response->data->core_values);
      $this->set('endorsementLimit', $response->data->endorsement_limit);

      //get emojis
      $response = $this->Apicalls->curlget("getEmojis.json", array());
      $response = json_decode($response);
      $response = $response->result;
      $this->set('emojis', $response->data);

      //emoji path
      $emojiUrl = strstr($response->data[0]->url, $response->data[0]->image, true);
      $emojiUrl = str_replace(DIRECTORY_SEPARATOR, "/", $emojiUrl);
      $this->set('emojiUrl', $emojiUrl);

      $this->set('allowAttachments', $loggedinUser['current_org']->allow_attachment);
      $this->set('allowComments', $loggedinUser['current_org']->allow_comments);

      //                pr($response->data->core_values);die;

      $this->set('type', $this->request->data['type']);
      } else {
      $postData = array("token" => $loggedinUser["token"]);
      //                        $postData['type'] = $this->request->data['type'];
      $postData['message'] = isset($this->request->data['message']) ? $this->request->data['message'] : "";
      //                        $postData['core_values'] = implode(",", $this->request->data['corevalue']);
      if(isset($this->request->data['emojis'])) {
      $postData['emojis'] = implode(",", $this->request->data['emojis']);
      }
      $endorseList = array();

      //                        foreach ($this->request->data['endorsee'] as $for => $endosedIds) {
      //                            foreach ($endosedIds as $endosedId) {
      //                                $endorseList[] = array("for" => $for, "id" => $endosedId);
      //                            }
      //                        }

      //                        $postData['endorse_list'] = json_encode($endorseList);
      //pr($postData);
      $response = $this->Apicalls->curlpost("endorse.json", $postData);
      $response = json_decode($response);
      $response = $response->result;
      if ($response->status == 1) {
      $postID = $response->data->post_id;
      echo json_encode(array('success' => true, "post_id" => $postID, "msg" => $response->msg));
      exit;
      } else {
      echo json_encode(array('success' => false, 'msg' => $response->msg));
      exit;
      }
      }
      } else {
      $this->redirect(array('controller' => 'endorse', 'action' => 'index'));
      }
      }
      }
      $this->set('addEndorse', false);
      } */

    public function add() {
//        pr($this->request->data);die;
        //$this->set('MenuName', 'nDorse Now');
        $loggedinUser = $this->Auth->user();
        $user_id = $loggedinUser["id"];
        if ($this->Session->check('Auth.User')) {
//            pr($loggedinUser['current_org']); exit;
            if (isset($loggedinUser['current_org'])) {
                if ($this->request->is('post')) {
//                    $this->request->data['type'] = 'standard';//remove this and above 1 condition
                    $this->set('jsIncludes', array('addEndorse'));

                    if (!isset($this->request->data['endorsee']) && isset($this->request->data['type'])) {
                        //get core values
                        $postData = array("token" => $loggedinUser["token"], 'org_id' => $loggedinUser['current_org']->id);
                        $Orgresponse = $this->Organization->findById($loggedinUser['current_org']->id);
                        $response = $this->Apicalls->curlpost("getVariousOrganizationData.json", $postData);
//                        pr($response); exit;
                        $response = json_decode($response);
                        $response = $response->result;
                        $this->set('coreValues', $response->data->core_values);
                        $this->set('endorsementLimit', $response->data->endorsement_limit);
                        $this->set('hashtags', $response->data->hashtags);

                        //get emojis
                        $data = array("token" => $loggedinUser["token"], 'org_id' => $loggedinUser['current_org']->id);
//                        pr($data);
                        $response = $this->Apicalls->curlpost("getOrgEmojis.json", $data);
                        //pr($response); //exit;
                        if (count($response) > 0) {
                            $response = json_decode($response);
                            if (isset($response->result)) {
                                $response = $response->result;
                                $this->set('emojis', $response->data_new);
                            } else {
                                $this->set('emojis', array());
                            }
                        } else {
                            $this->set('emojis', array());
                        }

//                        pr($loggedinUser['current_org']); exit;
                        //emoji path
                        $emojiUrl = "";
//                        pr($response->data); exit;
                        if (isset($response->data) && !empty($response->data)) {
                            $emojiUrl = strstr($response->data[0]->url, $response->data[0]->image, true);
                            $emojiUrl = str_replace(DIRECTORY_SEPARATOR, "/", $emojiUrl);
                        }
                        $this->set('emojiUrl', $emojiUrl);

                        if (!empty($Orgresponse)) {
                            $this->set('allowAttachments', $Orgresponse['Organization']['allow_attachment']);
                            $this->set('allowComments', $Orgresponse['Organization']['allow_comments']);
                            $this->set('optionalComments', $Orgresponse['Organization']['optional_comments']);
                            $this->set('endorseMessageMinLimit', $Orgresponse['Organization']['endorse_message_min_limit']);
                        } else {
                            $this->set('allowAttachments', $loggedinUser['current_org']->allow_attachment);
                            $this->set('allowComments', $loggedinUser['current_org']->allow_comments);
                            $this->set('optionalComments', $loggedinUser['current_org']->optional_comments);
                            $this->set('endorseMessageMinLimit', $loggedinUser['current_org']->endorse_message_min_limit);
                        }


                        if (isset($this->request->data['userid']) && ($this->request->data['userid'] != '')) {
                            $selected_user_id = $this->request->data['userid'];
                            $conditionarray = $array = array();
                            $conditionarray = array("User.id" => $selected_user_id);


                            $array['fields'] = array('fname', 'lname', 'userOrganization.subcenter_id');

                            $array['joins'] = array(
                                array(
                                    'table' => 'user_organizations',
                                    'alias' => 'userOrganization',
                                    'type' => 'LEFT',
                                    'conditions' => array(
                                        'userOrganization.user_id = User.id',
                                        'userOrganization.organization_id =' . $loggedinUser['current_org']->id,
                                    )
                                )
                            );
                            $array['conditions'] = $conditionarray;

                            $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                            $selectedUserData = $this->User->find("all", $array);
                            if (isset($selectedUserData[0]['User']) && !empty($selectedUserData[0]['User'])) {
                                $selectedUsername = $selectedUserData[0]['User']['fname'] . ' ' . $selectedUserData[0]['User']['lname'];
                                $selectUserSubcenterID = $selectedUserData[0]['userOrganization']['subcenter_id'];
                            }
                        } else {
                            $selected_user_id = "";
                            $selectedUsername = $selectUserSubcenterID = "";
                        }

                        $this->set('selectedUserId', $selected_user_id);
                        $this->set('selectedUsername', $selectedUsername);
                        $this->set('selectUserSubcenterID', $selectUserSubcenterID);
                        $this->set('type', $this->request->data['type']);
                    } else {
                        $postData = array("token" => $loggedinUser["token"]);
                        $postData['type'] = $this->request->data['type'];
                        $postData['message'] = isset($this->request->data['message']) ? $this->request->data['message'] : "";
                        $postData['core_values'] = implode(",", $this->request->data['corevalue']);
                        if (isset($this->request->data['emojis'])) {
                            $postData['emojis'] = implode(",", $this->request->data['emojis']);
                        }

                        $endorseList = array();

                        foreach ($this->request->data['endorsee'] as $for => $endosedIds) {
                            foreach ($endosedIds as $endosedId) {
                                $endorseList[] = array("for" => $for, "id" => $endosedId);
                            }
                        }


                        $hashtagArray = array();
                        if (isset($this->request->data['hashtags']) && $this->request->data['hashtags'] != "") {
                            $hashtagArray = explode(',', $this->request->data['hashtags']);
                            $this->request->data['hashtags'] = $hashtagArray;
                        }

                        $postData['endorse_list'] = json_encode($endorseList);
//                        pr($this->request->data['subcenter_for']);
                        $postData['subcenter_for'] = json_encode(array($this->request->data['subcenter_for']));

                        if (isset($this->request->data['hashtags'])) {
                            $postData['hashtags'] = json_encode($this->request->data['hashtags'], true);
                        }
                        //pr($this->request->data); exit;
//                        pr($postData); exit;
                        $response = $this->Apicalls->curlpost("endorse.json", $postData);
//                        pr($response);
//                        exit;
                        $response = json_decode($response);
                        $response = $response->result;
                        if ($response->status == 1) {
                            $endorsementIds = implode(",", $response->data->endorsement_ids);
                            echo json_encode(array('success' => true, "endorsementIds" => $endorsementIds, "msg" => $response->msg));
                            exit;
                        } else {
                            echo json_encode(array('success' => false, 'msg' => $response->msg));
                            exit;
                        }
                    }
                } else {
                    $this->redirect(array('controller' => 'endorse', 'action' => 'index'));
                }
            }
            $this->set('user_id', $user_id);
        }
        $this->set('addEndorse', false);
    }

    /* public function sendAttachments() {
      //        pr($_FILES);
      //        pr($this->data);die;
      $loggedinUser = $this->Auth->user();
      $postData = $this->request->data;
      $postData['type'] = 'image';
      $postData['token'] = $loggedinUser["token"];
      $postData['attachment'] = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
      //pr($postData);
      //        echo base64_encode($postData['attachment']);die;
      $response = $this->Apicalls->curlpost("saveEndorseAttachment.json", $postData);
      echo($response);die;
      $response = json_decode($response);
      $response = $response->result;

      if ($response->status == 1) {
      echo json_encode(array('success' => true));
      } else {
      echo json_encode(array('success' => false, "msg" => $response->msg));
      }

      exit;
      } */

    public function sendAttachments() {
//        pr($_FILES);
//        pr($this->data);die;
        $loggedinUser = $this->Auth->user();
        $postData = $this->request->data;
        $postData['type'] = 'image';
        $postData['token'] = $loggedinUser["token"];
        $postData['attachment'] = base64_encode(file_get_contents($_FILES['file']['tmp_name']));

//        echo base64_encode($postData['attachment']);die;
        $response = $this->Apicalls->curlpost("saveEndorseAttachment.json", $postData);
        echo($response);
        die;
        $response = json_decode($response);
        $response = $response->result;

        if ($response->status == 1) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false, "msg" => $response->msg));
        }

        exit;
    }

    public function sendAttachedFiles() {
//        pr($_FILES);
//        pr($this->data);die;
        $loggedinUser = $this->Auth->user();
        $postData = $this->request->data;
        $postData['type'] = 'files';
        $postData['token'] = $loggedinUser["token"];
        $originFileName = $_FILES['file']['name'];
        $fileName = $this->request->data['endorsement_ids'];
        $fileName = $fileName . "_" . time() . "." . $this->request->data['file_extension'];
        $pathToUpload = WWW_ROOT . ENDORSE_FILE_DIR . $fileName;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $pathToUpload)) {
            pr($_FILES['file']);
            $errors['moveUploaded'] = "move_uploaded_file() failed.";
            echo "move_uploaded_file() failed.";
            throw new Exception('Could not upload file');
            exit;
        }
        chmod($pathToUpload, 0777);
        $postData['fileName'] = $fileName;
        $postData['originFileName'] = $originFileName;
        //pr($postData);
//        echo base64_encode($postData['attachment']);die;
        $response = $this->Apicalls->curlpost("saveEndorseAttachmentFiles.json", $postData);
        echo($response);
        die;
        $response = json_decode($response);
        $response = $response->result;

        if ($response->status == 1) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false, "msg" => $response->msg));
        }

        exit;
    }

    function userlist() {
        $loggedinUser = $this->Auth->user();
//        pr($loggedinUser); exit;

        $this->set('jsIncludes', array('activeUserList'));

        if ($this->Session->check('Auth.User')) {
            if (isset($loggedinUser['current_org'])) {
                $postdata = array("token" => $loggedinUser["token"], "type" => "public");
                $jsondata = $this->Apicalls->curlpost("getActiveUserList.json", $postdata);

//                $postdata = array("token" => $loggedinUser["token"]);
//                $jsondata = $this->Apicalls->curlpost("getAllPendingListing.json", $postdata); // All pending notification list
//                $jsondata = $this->Apicalls->curlpost("getAllNotificationListing.json", $postdata); // All pending notification list
//                
//                $postdata = array("token" => $loggedinUser["token"], "id" => 4);
//                $jsondata = $this->Apicalls->curlpost("onViewNotification.json", $postdata); // On View notification
//                $postdata = array("token" => $loggedinUser["token"], "id" => 198, "type" => 'post');
//                $jsondata = $this->Apicalls->curlpost("getLikesList.json", $postdata); // On View notification
//                pr($jsondata);
//                exit;

                $jsondatadecoded = json_decode($jsondata, true);
//                pr($jsondatadecoded); exit;

                if ($jsondatadecoded["result"]["status"]) {
                    $activeuserdata = $jsondatadecoded["result"]["data"];
                    $this->set('activeuserdata', $activeuserdata['user']);
                    $this->set('total_pages', $activeuserdata["total_pages"]);
//                    $this->set('servertime', $endorsedatadata["server_time"]);
//                    $this->set('logged_user_id', $user_id);
//                    $this->set('user_role', $user_role);
//                    $this->set('org_user_role', $org_user_role);
//                    $this->set('user_do_not_remind', $user_do_not_remind);
                } else {
                    $errormsg = $jsondatadecoded["result"]["msg"];
                    $this->Session->write('error', $errormsg);
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }
            }
        }
        $this->set('MenuName', 'Acitve User List');
    }

    /*     * * DO NOT DELETE ****
      ADDED BY BABULAL PRASAD TO RECTIFY LIVE FEED @8-march-2018
     *      /
     */

//    public function updatefeedtrans(){
//        $this->loadModel('FeedTran');
//        $sql = "select feed_trans.*, endorsements.type  from feed_trans right join endorsements on (endorsements.id = feed_trans.feed_id ) where feed_type = 'endorse' order by type desc";
//        $LiveFeedEndorsements = $this->FeedTran->query($sql);
//        foreach($LiveFeedEndorsements as $index => $liveFeedData){
//            $feedTransID = $liveFeedData['feed_trans']['id'];
//            $endorsementsType = $liveFeedData['endorsements']['type'];
//            
//            $this->FeedTran->updateAll(array('endorse_type' => "'".$endorsementsType."'"), array('id' => $feedTransID, 'feed_type' => 'endorse'));
//            echo "FeedID : ".$feedTransID." /  Type : ".$endorsementsType."</br>";
//        }
//            echo "DONE"; exit;
//    }

    public function daisy() {
        $this->set('MenuName', 'Post Now');
        $loggedinUser = $this->Auth->user();
//        pr($loggedinUser); exit;
        $user_id = $loggedinUser['id'];
//        $org_details = $loggedinUser['current_org'];
//        pr($org_details);
        $orgID = $loggedinUser['current_org']->id;
        $data = $this->Organization->findById($orgID);
        $org_details = $data['Organization'];
//        pr($org_details);exit;
        $org_user_role = $loggedinUser['current_org']->org_role;
        $DAISYAwards = Configure::read("DAISY_Awards");
        if ($this->Session->check('Auth.User')) {
//            pr($loggedinUser['current_org']); exit;
            if (isset($loggedinUser['current_org'])) {

                //$response = $this->Apicalls->curlget("getEmojis.json", array());
                $data = array("token" => $loggedinUser["token"], 'org_id' => $loggedinUser['current_org']->id);
//                        pr($data);
                $response = $this->Apicalls->curlpost("getOrgEmojis.json", $data);
                //pr($response); exit;
                $response = json_decode($response);
                $response = $response->result;
                $this->set('emojis', $response->data);
                $this->set('jsIncludes', array('addDaisy'));
                //emoji path
//                pr($response); exit;
                $emojiUrl = strstr($response->data[0]->url, $response->data[0]->image, true);
                $emojiUrl = str_replace(DIRECTORY_SEPARATOR, "/", $emojiUrl);

                $this->set('emojiUrl', $emojiUrl);

                $postData = array("token" => $loggedinUser["token"], 'org_id' => $loggedinUser['current_org']->id);

                $orgCoreValues = $this->Common->getOrgDAISYCoreValuesAndCode($orgID);
                $this->set('coreValues', $orgCoreValues);
                ?>

                <?php

                if ($this->request->is('post')) {

//                    $this->request->data['type'] = 'standard';//remove this and above 1 condition
//                    $loggedinUser = $this->Auth->user();
//                    pr($loggedinUser);
//                    pr($this->request->data);
                    if (!isset($this->request->data['endorsee'])) {
                        //Create Guest User 
                        $UserData['email'] = '';
                        $UserData['username'] = '';
                        $UserData['fname'] = isset($this->request->data['endorsement']['searchKey']) ? $this->request->data['endorsement']['searchKey'] : '';
                        $UserData['lname'] = isset($this->request->data['endorsement']['lastname']) ? $this->request->data['endorsement']['lastname'] : '';
                        $UserData['mobile'] = '';
                        $UserData['source'] = 'daisy';
                        $UserData['status'] = '0';
                        $UserData['role'] = '5';
                        $UserData['password'] = 'aba2d5949a122c89cbfbd676ab814333d2615df5'; //12345678 Static password
                        $guestUser = $this->User->save($UserData);
                        $endorse_id = $this->User->id;
                        $this->request->data['endorsee']['user'][] = $endorse_id;
                        $this->Session->write('Auth.User', $loggedinUser);
//                        $loggedinUser = $this->Auth->user();
//                        pr($loggedinUser);
//                        exit;
                    }

                    //else {
//                        pr($this->request->data);

                    $postData = array("token" => $loggedinUser["token"]);
                    $postData['type'] = 'daisy';
                    $postData['department_name'] = isset($this->request->data['endorsement']['department_unit']) ? $this->request->data['endorsement']['department_unit'] : 0;
                    $postData['department_id'] = isset($this->request->data['department_unit_id']) ? $this->request->data['department_unit_id'] : 0;

                    $postData['message'] = isset($this->request->data['message']) ? $this->request->data['message'] : "";
                    $postData['core_values'] = '';
                    if (isset($this->request->data['corevalue']) && !empty($this->request->data['corevalue'])) {
                        $postData['core_values'] = implode(",", $this->request->data['corevalue']);
                    }

                    $postData['award_type'] = isset($this->request->data['award_type']) ? $this->request->data['award_type'] : 0;
                    $postData['org_id'] = isset($this->request->data['org_id']) ? $this->request->data['org_id'] : 0;
                    $postData['source'] = isset($this->request->data['source']) ? $this->request->data['source'] : 'web_app';

                    if (isset($this->request->data['emojis'])) {
                        $postData['emojis'] = implode(",", $this->request->data['emojis']);
                    }

                    $endorseList = array();

                    foreach ($this->request->data['endorsee'] as $for => $endosedIds) {
                        foreach ($endosedIds as $endosedId) {
                            $endorseList[] = array("for" => 'user', "id" => $endosedId);
                        }
                    }

                    $postData['endorse_list'] = json_encode($endorseList);
//                        pr($postData);
                    //exit;
                    $response = $this->Apicalls->curlpost("daisyEndorse.json", $postData);
//                        pr($response); exit;
                    $response = json_decode($response);
                    $response = $response->result;
                    if ($response->status == 1) {
                        $endorsementIds = implode(",", $response->data->endorsement_ids);
                        echo json_encode(array('success' => true, "endorsementIds" => $endorsementIds, "msg" => $response->msg));
                        exit;
                    } else {
                        echo json_encode(array('success' => false, 'msg' => $response->msg));
                        exit;
                    }
                    //}
                }
            }
        }
        $this->set(compact("org_user_role", 'user_id', 'org_details', 'DAISYAwards'));
        $this->set('addPost', false);
    }

    public function addcomment() {
//        $this->autoRender = false;
//        $this->layout = false;
        $loggedinUser = $this->Auth->user();

        if ($this->Session->check('Auth.User')) {
            if (isset($loggedinUser['current_org'])) {
                if ($this->request->is('post')) {

//                    $this->request->data['type'] = 'standard';//remove this and above 1 condition
//                    $this->set('jsIncludes', array('addPostComment'));

                    if (!empty($this->request->data)) {
                        $postData = array("token" => $loggedinUser["token"]);
//                        $postData['type'] = $this->request->data['type'];
                        $postData['comment'] = isset($this->request->data['message']) ? $this->request->data['message'] : "";
                        $postData['endorsement_id'] = $this->request->data['endorsement_id'];
                        $postData['org_id'] = $loggedinUser['current_org']->id;
                        //$postData['post_id'] = '43';
                        //pr($postData);
                        $response = $this->Apicalls->curlpost("postEndorseComment.json", $postData);
//                        pr($response);
//                        exit;

                        $response = json_decode($response, true);

                        $response = $response['result'];

                        $userName = $loggedinUser['fname'] . ' ' . $loggedinUser['lname'];
                        $userImage = $loggedinUser['image'];

                        if ($response['status'] == 1) {
                            $postCommentData = $response['data'];
                            $postCommentData['EndorsementComment']['username'] = $userName;
                            $postCommentData['EndorsementComment']['user_image'] = $userImage;
                            $this->set('endorseCommentData', $postCommentData);

                            //$postID = implode(",", $postID);
//                            echo json_encode(array('success' => true, "post_id" => $postID, "msg" => $response->msg));
//                            exit;
                        } else {
//                            echo json_encode(array('success' => false, 'msg' => $response->msg));
//                            exit;
                        }
                    }
                }
            }
        }
    }

    // endorsements
    public function summary() {
        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('Auth.User')) {

            if (isset($loggedinUser['current_org'])) {

                $postdata = array("token" => $loggedinUser["token"]);
                $jsondata = $this->Apicalls->curlpost("endorsestats.json", $postdata);
                $jsondatadecoded = json_decode($jsondata, true);

                if ($jsondatadecoded["result"]["status"]) {
                    $endorsedatadata = $jsondatadecoded["result"]["data"];
                    $this->set('statesdata', $endorsedatadata);
                    $this->set('profiledata', $loggedinUser);
                } else {
                    $errormsg = $jsondatadecoded["result"]["msg"];
                    $this->Session->write('error', $errormsg);
                    $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
                }

                //Getting data for ndorsement received
                $postdata = array("token" => $loggedinUser["token"], "type" => "endorsed");
                $jsondata = $this->Apicalls->curlpost("getEndorseList.json", $postdata);
                $jsondatadecoded = json_decode($jsondata, true);
                if ($jsondatadecoded["result"]["status"]) {
                    $endorsedatadata = $jsondatadecoded["result"]["data"];
                    $this->set('endorsedata', $endorsedatadata["endorse_data"]);
                    $this->set('endorsepage', $endorsedatadata["total_page"]);
                    $this->set('servertime', $endorsedatadata["server_time"]);
                }

                //Getting data for ndorsements given
                $postdata = array("token" => $loggedinUser["token"], "type" => "endorser");
                $jsondata = $this->Apicalls->curlpost("getEndorseList.json", $postdata);

                $jsondatadecoded = json_decode($jsondata, true);
//                pr($jsondatadecoded); exit;
                if ($jsondatadecoded["result"]["status"]) {
                    $endorsedatadata = $jsondatadecoded["result"]["data"];
                    $this->set('endorsedata2', $endorsedatadata["endorse_data"]);
                    $this->set('endorsepage2', $endorsedatadata["total_page"]);
                    $this->set('servertime2', $endorsedatadata["server_time"]);
                }
            } else {
                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            }
        } else {
            $this->redirect(array('controller' => 'client', 'action' => 'login'));
        }

        $org_user_role = $loggedinUser['current_org']->org_role;
        $logged_user_id = $loggedinUser['id'];
        $this->set('jsIncludes', array('endorse_stats', 'endorse-ndorsed-section', 'endorse-ndorser-section', 'editEndorsementMessage'));
        $this->set('addEndorse', true);
        $this->set('org_user_role', $org_user_role);
        $this->set('logged_user_id', $logged_user_id);
        $this->set('MenuName', 'nDorsements');
    }

}
?>
