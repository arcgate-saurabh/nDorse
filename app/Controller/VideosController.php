<?php

class VideosController extends AppController {

    public $components = array('RequestHandler', "Auth", "Common", "Session", "Apicalls");

    public function beforeFilter() {

        parent::beforeFilter();
        $this->layout = "clientlayout";
        $this->Auth->deny();
        $loggedinUser = $this->Auth->user();

        if (isset($loggedinUser['org_updates']) && ($loggedinUser['org_updates']['org_status'] != 'active' || $loggedinUser['org_updates']['user_status'] != "active")) {
            $this->redirect(array('controller' => 'client', 'action' => 'inactiveOrg'));
        }
    }

    public function beforeRender() {
        $loggedinUser = $this->Auth->user();
        if ($loggedinUser['current_org']->joined == 0) {
            $currentOrg = $loggedinUser['current_org'];
            $currentOrg->joined = 1;
            $this->Session->write('Auth.User.current_org', $currentOrg);
        }
    }

    public function add() {
        $this->set('MenuName', 'Post Now');
        $loggedinUser = $this->Auth->user();
        //pr($loggedinUser); exit;
        $org_user_role = $loggedinUser['current_org']->org_role;
        if ($this->Session->check('Auth.User')) {

            if (isset($loggedinUser['current_org'])) {
                $response = $this->Apicalls->curlget("getEmojis.json", array());
                $response = json_decode($response);
                $response = $response->result;
                $this->set('emojis', $response->data);
                $this->set('jsIncludes', array('addPost'));
                //emoji path
                $emojiUrl = strstr($response->data[0]->url, $response->data[0]->image, true);
                $emojiUrl = str_replace(DIRECTORY_SEPARATOR, "/", $emojiUrl);
                $this->set('emojiUrl', $emojiUrl);
                ?>

                <?php

                if ($this->request->is('post')) {
//                    pr($this->request->data);exit;
//                    $this->request->data['type'] = 'standard';//remove this and above 1 condition


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
//                        pr($this->request->data);


                        $postData = array("token" => $loggedinUser["token"]);
                        if (isset($this->request->data['report_type'])) {
                            $postData['post_type'] = $this->request->data['report_type'];
                        }
                        if (isset($this->request->data['post_date'])) {
                            $postData['post_date'] = $this->request->data['post_date'];
                        }
                        if (isset($this->request->data['post_time'])) {
                            $postData['post_time'] = $this->request->data['post_time'];
                        }
                        if (isset($this->request->data['post_time'])) {
                            $postData['post_time'] = $this->request->data['post_time'];
                        }
                        if (isset($this->request->data['push_notification'])) {
                            $postData['push_notification'] = $this->request->data['push_notification'];
                        }
                        $sendEmail = 0;
                        if (isset($this->request->data['email_notification'])) {
                            $sendEmail = $postData['email_notification'] = $this->request->data['email_notification'];
                        }

                        if (isset($this->request->data['usertimzone'])) {
                            $postData['usertimzone'] = $this->request->data['usertimzone'];
                        }
//                        $postData['type'] = $this->request->data['type'];
                        $postData['message'] = isset($this->request->data['message']) ? $this->request->data['message'] : "";
                        $postData['title'] = isset($this->request->data['title']) ? $this->request->data['title'] : "";
//                        $postData['core_values'] = implode(",", $this->request->data['corevalue']);
                        if (isset($this->request->data['emojis'])) {
                            $postData['emojis'] = implode(",", $this->request->data['emojis']);
                        }

                        if (isset($this->request->data['endorsee'])) {
                            foreach ($this->request->data['endorsee'] as $for => $endosedIds) {
                                foreach ($endosedIds as $endosedId) {
                                    $endorseList[] = array("for" => $for, "id" => $endosedId);
                                }
                            }
                            $postData['endorse_list'] = json_encode($endorseList);
                        }
//                        pr($postData);exit;
                        $response = $this->Apicalls->curlpost("wallpost.json", $postData);
//                        pr($response);
//                        exit;
                        $response = json_decode($response);
                        $response = $response->result;
                        if ($response->status == 1) {
                            $postID = $response->data->post_id;
                            //$postID = implode(",", $postID);
                            echo json_encode(array('success' => true, "post_id" => $postID, "msg" => $response->msg));
                            exit;
                        } else {
                            echo json_encode(array('success' => false, 'msg' => $response->msg));
                            exit;
                        }
                    }
                }
            }
        }
        $this->set(compact("org_user_role"));
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
                    $this->set('jsIncludes', array('addPostComment'));

                    if (!empty($this->request->data)) {
                        $postData = array("token" => $loggedinUser["token"]);
//                        $postData['type'] = $this->request->data['type'];
                        $postData['comment'] = isset($this->request->data['message']) ? $this->request->data['message'] : "";
                        $postData['post_id'] = $this->request->data['post_id'];
                        //$postData['post_id'] = '43';
                        //pr($postData);
                        $response = $this->Apicalls->curlpost("postComment.json", $postData);
                        $response = json_decode($response, true);

                        $response = $response['result'];

                        $userName = $loggedinUser['fname'] . ' ' . $loggedinUser['lname'];
                        $userImage = $loggedinUser['image'];

                        if ($response['status'] == 1) {
                            $postCommentData = $response['data'];
                            $postCommentData['PostComment']['username'] = $userName;
                            $postCommentData['PostComment']['user_image'] = $userImage;
                            $this->set('postCommentData', $postCommentData);

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

    public function sendAttachments() {
//        pr($_FILES);
//        pr($this->data);die;
        $loggedinUser = $this->Auth->user();
        $postData = $this->request->data;
        $postData['type'] = 'image';
        $postData['token'] = $loggedinUser["token"];
        $postData['attachment'] = base64_encode(file_get_contents($_FILES['file']['tmp_name']));
        //pr($postData);
//        echo base64_encode($postData['attachment']);die;
        $response = $this->Apicalls->curlpost("saveWallpostAttachment.json", $postData);
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
        $fileName = $this->request->data['post_id'];
        $fileName = $fileName . "_" . time() . "." . $this->request->data['file_extension'];
        $pathToUpload = WWW_ROOT . POST_FILE_DIR . $fileName;

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
        $response = $this->Apicalls->curlpost("saveWallpostAttachmentFiles.json", $postData);
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

    public function index() {

        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('Auth.User')) {
            if (isset($loggedinUser['current_org'])) {
                $postdata = array("token" => $loggedinUser["token"], "type" => "public");
                $jsondata = $this->Apicalls->curlpost("getWallPostList.json", $postdata);
                //pr($jsondata); exit;
                $jsondatadecoded = json_decode($jsondata, true);

                if ($jsondatadecoded["result"]["status"]) {
                    $endorsedatadata = $jsondatadecoded["result"]["data"];

                    $this->set('postdata', $endorsedatadata["post_data"]);
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

        $this->set('jsIncludes', array('wallpost'));
        //$this->set('addEndorse', true);
        $this->set('MenuName', 'Live Feed');
    }

    public function pending() {

        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('Auth.User')) {
            if (isset($loggedinUser['current_org'])) {
                $postdata = array("token" => $loggedinUser["token"], "type" => "public");
                //$jsondata = $this->Apicalls->curlpost("getWallPostList.json", $postdata);
                $jsondata = $this->Apicalls->curlpost("getPendingPostsList.json", $postdata);
                //pr($jsondata); exit;
                $jsondatadecoded = json_decode($jsondata, true);

                if ($jsondatadecoded["result"]["status"]) {
                    $endorsedatadata = $jsondatadecoded["result"]["data"];
                    $this->set('postdata', $endorsedatadata["pending_posts"]);
                    $this->set('endorsepage', $endorsedatadata["total_page"]);
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

        $this->set('jsIncludes', array('pendingPost'));
        //$this->set('addEndorse', true);
        $this->set('MenuName', 'Pending Posts');
        $this->set('loggedinUser', $loggedinUser);
    }

    public function edit($id) {
        $this->set('MenuName', 'Post Now');
        $loggedinUser = $this->Auth->user();
        //pr($loggedinUser); exit;
        $org_user_role = $loggedinUser['current_org']->org_role;
        if ($this->Session->check('Auth.User')) {
            $postdata = array("token" => $loggedinUser["token"], "post_id" => $id);
//            $jsondata = $this->Apicalls->curlpost("wallPostdetails.json", $postdata);


            if ($this->request->is('post')) {

                if (isset($this->request->data['post_id'])) {
                    $postId = $this->request->data['post_id'];
                    $postScheduleId = $this->request->data['post_schedule_id'];
                    $feedTransId = $this->request->data['feed_trans_id'];
                    $postType = $this->request->data['report_type'];
                    $postDate = $this->request->data['post_date'];
                    $postTime = $this->request->data['post_time'];
                    $postTitle = $this->request->data['title'];
                    $postMessage = $this->request->data['message'];

//                    echo $postUserTimezone; exit;
                    $postData['post_id'] = $postId;
                    $postData['post_schedule_id'] = $postScheduleId;
                    $postData['feed_trans_id'] = $feedTransId;
                    $postData['post_type'] = $postType;
                    $postData['post_date'] = $postDate;
                    $postData['post_time'] = $postTime;

                    if ($postData['post_type'] == 'postlater') {
                        if (isset($this->request->data['post_date']) && $this->request->data['post_date'] != '') {
                            if (isset($this->request->data['post_time']) && $this->request->data['post_time'] != '') {
                                $datetimeToSave = $this->Common->daterangeAndTimeToSQL($this->request->data['post_date'], $this->request->data['post_time']);
                                $usertimzone = 'UTC';
                                if (isset($this->request->data['usertimzone']) && $this->request->data['usertimzone'] != '') {
                                    $usertimzone = $this->request->data['usertimzone'];
                                }
                                $UTCTimeToPost = $this->Common->ConvertOneTimezoneToAnotherTimezone($datetimeToSave, $usertimzone, 'UTC');
                            }
                        }
                        $postData['post_publish_date'] = $UTCTimeToPost;
                    } else {
                        $postData['post_publish_date'] = date("Y-m-d H:i:s", time());
                    }


                    $postData['title'] = $postTitle;
                    $postData['message'] = $postMessage;

                    // $response = $this->Apicalls->curlpost("wallpostupdate.json", $postData);  //Create API TO UPDATE
//                    pr($postScheduleData);
//                    pr($postData);
//                    exit;
                    $push_notification = 0;
                    if (isset($this->request->data['push_notification']) && $this->request->data['push_notification'] == 'active') {
                        $push_notification = 1;
                    }

                    $postData['push_notification'] = $push_notification;

                    if ($postData['post_type'] == 'postlater') {
                        $postStatus = $feedTransStatus = 2;
                        $postScheduleStatus = 0;
                        $scheduled = 1;
                        $deleteScheduled = 0;


                        if (isset($this->request->data['post_date']) && $this->request->data['post_date'] != '') {
                            $dateToSave = $this->Common->daterangeToSQL($this->request->data['post_date']);
                            if (isset($this->request->data['post_time']) && $this->request->data['post_time'] != '') {
                                $datetimeToSave = $this->Common->daterangeAndTimeToSQL($this->request->data['post_date'], $this->request->data['post_time']);
                                $timeToSave = $this->request->data['post_time'] . ":00";
                                $usertimzone = 'UTC';
                                if (isset($this->request->data['usertimzone']) && $this->request->data['usertimzone'] != '') {
                                    $usertimzone = $this->request->data['usertimzone'];
                                }
                                $UTCTimeToPost = $this->Common->ConvertOneTimezoneToAnotherTimezone($datetimeToSave, $usertimzone, 'UTC');
                            }
                            $PostSchedule['PostSchedule']['post_id'] = $postId;
                            $PostSchedule['PostSchedule']['date'] = $dateToSave;
                            $PostSchedule['PostSchedule']['time'] = $timeToSave;
                            $PostSchedule['PostSchedule']['datetime'] = $datetimeToSave;
//                            $feedTrans['FeedTran']['publish_date'] = $datetimeToSave;
                            $feedTrans['FeedTran']['publish_date'] = $UTCTimeToPost;
                            $PostSchedule['PostSchedule']['utc_post_datetime'] = $UTCTimeToPost;
                        }
                    } else if ($postData['post_type'] == 'postnow') {
                        $postStatus = $feedTransStatus = 1;
                        $scheduled = 0;
                        $deleteScheduled = 1;
                        $datetimeToSave = date("Y-m-d H:i:s", time());
                    }
                    $postArray = array();
                    /* update data in posts table */
                    $this->loadModel('Post');
                    $this->Post->id = $postId;
                    $postArray['title'] = "'" . $postTitle . "'";
                    $postArray['message'] = "'" . $postMessage . "'";
                    $postArray['scheduled'] = $scheduled;
                    $postArray['push_notification'] = $push_notification;
                    $postArray['status'] = $postStatus;
                    $res = $this->Post->updateAll($postArray, array('id' => $postId));


                    /*  Update PostSchedules Table */
                    $this->loadModel('PostSchedule');
                    if (isset($deleteScheduled) && $deleteScheduled == 1) {
                        /* deleting post schedule if post now selected */
                        $this->PostSchedule->id = $postScheduleId;
                        $this->PostSchedule->delete(array('id' => $postScheduleId));
                    } else { //Postlater Code
                        $postScheduleData = $this->PostSchedule->findByPostId($postId);
                        if (isset($postScheduleData) && count($postScheduleData) > 0) {
                            $postScheduleID = $postScheduleData['PostSchedule']['id']; /* update data in post_schedules table */
                            $this->PostSchedule->id = $postScheduleId;
                            $this->PostSchedule->updateAll(array('date' => "'" . $dateToSave . "'", 'time' => "'" . $timeToSave . "'",
                                'datetime' => "'" . $datetimeToSave . "'", 'status' => $postScheduleStatus, 'utc_post_datetime' => "'" . $UTCTimeToPost . "'"), array('id' => $postScheduleId));
//                            echo $this->PostSchedule->getLastQuery();die;
                        } else {
                            $dateDATA = $this->PostSchedule->save($PostSchedule);
                        }
                    }


                    /* update data in feed trans table */ // status & publish date
                    $this->loadModel('FeedTran');

                    $this->FeedTran->id = $feedTransId;
                    $this->FeedTran->updateAll(array('publish_date' => "'" . $datetimeToSave . "'", 'status' => $feedTransStatus), array('id' => $feedTransId));
                }

                echo json_encode(array('success' => true, "post_id" => $postId, "msg" => "updated successfully"));
                exit;
            }


            $jsondata = $this->Apicalls->curlpost("getPendingwallPostdetails.json", $postdata);
            $jsondatadecoded = json_decode($jsondata, true);
            if ($jsondatadecoded["result"]["status"]) {
                $postresultdata = $jsondatadecoded["result"]["data"];
                $this->set('postdata', $postresultdata);
                $this->set('loggeduserimage', $loggedinUser['image']);
                $this->set('logged_user_id', $loggedinUser['id']);

                //pr($postresultdata['FeedTran']);exit;
                $visibleFiltersTags = array();
                if (isset($postresultdata['FeedTran']['visibility_check']) && count($postresultdata['FeedTran']['visibility_check']) > 0) {
                    if (isset($postresultdata['FeedTran']['visible_dept']) && count($postresultdata['FeedTran']['visible_dept']) > 0) {
                        $visibleDeptArray = json_decode($postresultdata['FeedTran']['visible_dept']);
                        $visibleDepts = $this->Common->getDeptNameByIds($visibleDeptArray);
                        $visibleFiltersTags[] = $visibleDepts;
//                        pr($visibleDeptArray);
                    }
                    if (isset($postresultdata['FeedTran']['visible_sub_org']) && count($postresultdata['FeedTran']['visible_sub_org']) > 0) {
                        $visibleSubOrgArray = json_decode($postresultdata['FeedTran']['visible_sub_org']);
                        $visibleSubOrg = $this->Common->getSubOrgNameByIds($visibleSubOrgArray);
                        $visibleFiltersTags[] = $visibleSubOrg;
//                        pr($visibleSubOrgArray);
                    }

                    if (isset($postresultdata['FeedTran']['visible_user_ids']) && count($postresultdata['FeedTran']['visible_user_ids']) > 0) {
                        $visibleUsersArray = json_decode($postresultdata['FeedTran']['visible_user_ids']);
                        $visibleUsers = $this->Common->getUsersNameByIds($visibleUsersArray);
                        $visibleFiltersTags[] = $visibleUsers;
//                        pr($visibleUsersArray);
                    }
                }

                $this->set("visibleFiltersTags", $visibleFiltersTags);
            } else {
                $errormsg = $jsondatadecoded["result"]["msg"];
                $this->Session->write('error', $errormsg);
                $this->redirect(array('controller' => 'client', 'action' => 'setOrg'));
            }

//            pr($jsondatadecoded); 
//            exit;
            if (isset($loggedinUser['current_org'])) {
                $response = $this->Apicalls->curlget("getEmojis.json", array());
                $response = json_decode($response);
                $response = $response->result;
                $this->set('emojis', $response->data);
                //$this->set('jsIncludes', array('addPost'));
                //emoji path
                $emojiUrl = strstr($response->data[0]->url, $response->data[0]->image, true);
                $emojiUrl = str_replace(DIRECTORY_SEPARATOR, "/", $emojiUrl);
                $this->set('emojiUrl', $emojiUrl);
            }
        }
        $this->set(compact("org_user_role"));
        $this->set('addPost', false);
        $this->set('jsIncludes', array('editPost'));
    }

    public function details($id) {
        $loggedinUser = $this->Auth->user();

        if ($this->Session->check('Auth.User')) {
            if (isset($loggedinUser['current_org'])) {
                $postdata = array("token" => $loggedinUser["token"], "post_id" => $id);
                //$this->set('jsIncludes', array('addPostComment'));

                $jsondata = $this->Apicalls->curlpost("wallPostdetails.json", $postdata);
                $jsonCommentdata = $this->Apicalls->curlpost("getWallPostCommentLists.json", $postdata);
//                pr($jsondata);
//                exit;
                $jsondatadecoded = json_decode($jsondata, true);
                $jsonCommentdatadecoded = json_decode($jsonCommentdata, true);
                //pr($jsonCommentdata);exit;
                if ($jsondatadecoded["result"]["status"]) {
                    $postresultdata = $jsondatadecoded["result"]["data"];


                    $this->set('postdata', $postresultdata);
                    $this->set('postCommentData', $jsonCommentdatadecoded);
                    $this->set('loggeduserimage', $loggedinUser['image']);
                    $this->set('logged_user_id', $loggedinUser['id']);
                    //print_r($endorsedatadata);exit;
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
        $this->set('MenuName', 'nDorsement Detail');
        $this->set('jsIncludes', array('endorse_details', 'addPostComment'));
    }

    public function loadmorecomments() {
//        $this->autoRender = false;
//        $this->layout = false;
        $loggedinUser = $this->Auth->user();
        if ($this->Session->check('Auth.User')) {
            if (isset($loggedinUser['current_org'])) {
                if ($this->request->is('post')) {

                    if (!empty($this->request->data)) {
                        $postData = array("token" => $loggedinUser["token"]);
                        $postData['post_id'] = $this->request->data['post_id'];
                        $postData['page'] = $this->request->data['page_no'];

                        $response = $this->Apicalls->curlpost("getWallPostCommentLists.json", $postData);
                        $response = json_decode($response, true);

                        $response = $response['result'];

                        $userName = $loggedinUser['fname'] . ' ' . $loggedinUser['lname'];
                        $userImage = $loggedinUser['image'];

                        if ($response['status'] == 1) {
                            $postCommentData = $response['data'];
                            $postCommentData['PostComment']['username'] = $userName;
                            $postCommentData['PostComment']['user_image'] = $userImage;
                            $this->set('postCommentData', $postCommentData);

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

    /** Added By Babulal Prasad to download attached files ** */
    public function download() {
        $this->autoRender = false;
        $filename = $this->request->params['pass'][3];
        $orgfilename = $this->request->params['pass'][4];
//        pr($this->request->params); exit;
        $this->response->file(
                'webroot/uploads/post/files/' . $filename, array(
            'download' => true,
            'name' => $orgfilename
                )
        );
//        return $this->response;
    }

    public function temp() {
        
    }

}
?>
