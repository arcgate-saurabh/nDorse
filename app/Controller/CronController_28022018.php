<?php

class CronController extends AppController {

    public $components = array("Common");
    public $uses = array("Email", "Trophy", "Endorsement", "Badge", "ErrorEmail", "UserOrganization", "Verification", "PasswordCode", "Organization", "LoginStatistics", "EndorseCoreValue", "Topendorser", "Subscription", "EmailMigration", "Subscription", "Transaction", "EndorseReplies", "Archive", "PendingEmail", "TempEmail", "PostSchedule");

    public function beforeFilter() {
        parent::beforeFilter();
        $this->layout = null;
        $this->Auth->allow('emailcron', "countingBadgesOnRecieve", "durationBadgeRecieve", "durationBadgeSent", "errorEmailCron", "errorEmailCronDisplay", "endorseEmails", "verificationEmails", "forgotPasswordEmails", "pushnotify", "topendorse", "topendorsemonth", "topendorsed", "weekelyEndorseNotification", "orghealth", "weekelyEndorseAlert", "subscriptionReminderCron", "migrationEmailCron", "appUseReminderCron", "updateCancelledSubscription", "pushnotifyandroid", "syncBadgeCount", "subscriptionexpire", "globalemailcron", "syncBadgeTopEndorsed", "syncBadgeTopEndorser", "replynotify", "tempEmailcron", "postPushNotification");
        if (isset($this->Security) &&
                ($this->action == 'emailcron' ||
                $this->action == 'globalemailcron' ||
                $this->action == 'updateCancelledSubscription' ||
                $this->action == 'migrationEmailCron' ||
                $this->action == 'tempEmailcron' ||
                $this->action == 'appUseReminderCron' ||
                $this->action == 'errorEmailCron' ||
                $this->action == 'errorEmailCronDisplay' ||
                $this->action == 'verificationEmails' ||
                $this->action == 'forgotPasswordEmails' ||
                $this->action == 'endorseEmails' ||
                $this->action == 'durationBadgeRecieve' ||
                $this->action == 'durationBadgeSent' ||
                $this->action == 'weekelyEndorseNotification' ||
                $this->action == 'subscriptionReminderCron' ||
                $this->action == 'topendorseweek' ||
                $this->action == 'topendorsemonth' ||
                $this->action == 'syncBadgeCount' ||
                $this->action == 'syncBadgeTopEndorsed' ||
                $this->action == 'syncBadgeTopEndorser' ||
                $this->action == 'countingBadgesOnRecieve')) {
            $this->Security->validatePost = false;
            $this->Security->csrfCheck = false;
        }
    }

    public function emailcron() {
        ini_set('memory_limit', '2G');
        $params = array(
            'fields' => array(),
            'conditions' => array('mail_sent' => 0),
            'order' => array('Email.created ASC'),
            'limit' => 50
        );

        $emailData = $this->Email->find('all', $params);

        if (!empty($emailData)) {
            $updateEmailIds = array();
            foreach ($emailData as $email) {
                $updateEmailIds[] = $email['Email']['id'];
            }

            $mailSentUpdated = $this->Email->updateAll(array('mail_sent' => 1), array('id' => $updateEmailIds));

            if ($mailSentUpdated) {
                foreach ($emailData as $data) {
                    $mailSent = false;
                    //                $this->log($data, "registeremaillogs");

                    if (filter_var($data['Email']['to'], FILTER_VALIDATE_EMAIL)) {
                        echo $to = $data['Email']['to'];
                        $subject = $data['Email']['subject'];
                        $viewVars = unserialize($data['Email']['config_vars']);
                        $template = $data['Email']['template'];
                        $bcc = isset($data['Email']['bcc']) ? $data['Email']['bcc'] : array();
                        $cc = isset($data['Email']['cc']) ? $data['Email']['cc'] : array();
                        $attatched = 0;
                        if (isset($viewVars["attatched"]) && ($viewVars["attatched"] == 1)) {
                            $attatched = 1;
                        }

                        //Save emails to temporary table if not register, verfication, invite or forgot password
//                        $this->TempEmail->clear();
//                        $templateArray = array("invite", "invitation_admin", "register", "verification", "forgot_password");
//        
//                        $template = trim($template);
//
//                        if(!in_array($template, $templateArray)) {
//                            $saved = $this->TempEmail->save($data['Email']);
//                            if($saved) {
////                                pr($saved);
//                                $deleted = $this->Email->delete($data['Email']['id']);
//                                echo "   ---------   Saved in temporary emails . Subject : " . $subject;
//                                echo "<hr>";
//                            }
//                            continue;
//                        }


                        $saved = 0;
                        $savedArchive = 0;

                        $archiveData = $data['Email'];
                        $archiveData['email_id'] = $data['Email']['id'];
                        unset($archiveData['id']);

                        $conditions = array();
                        $conditions['LOWER(Archive.to)'] = strtolower($to);
                        $conditions['subject'] = $subject;
                        $conditions['TIMESTAMPDIFF(SECOND,  updated, NOW())  < '] = (24 * 60 * 60);
                        $alreadySentCount = $this->Archive->find("count", array("conditions" => $conditions));
                        //                    echo "<br>" . $this->Archive->getLastQuery();die;
                        //                    pr($alreadySent);die;

                        if ($alreadySentCount >= 5) {
                            $this->PendingEmail->clear();
                            $saved = $this->PendingEmail->save($archiveData);
                            if ($saved) {
//                                pr($saved);
                                $deleted = $this->Email->delete($data['Email']['id']);
                                echo "   ---------   Saved in pending emails . Subject : " . $subject;
                            }
                        } else {
                            $this->Archive->clear();
                            $savedArchive = $this->Archive->save($archiveData);

                            if ($savedArchive) {
//                                pr($savedArchive);
                                $deleted = $this->Email->delete($data['Email']['id']);
                                echo "   ---------   Saved in archive emails . Subject : " . $subject;



                                if ($deleted) {
                                    if (trim($template) == "org_admin_access_action") {
                                        $mailSent = $this->orgadminaccessemail($data);
                                        //exit;
                                    } elseif (trim($template) == "org_status_action") {
                                        $mailSent = $this->orgadminaactionemail($data);
                                    } elseif ($attatched == 1) {
                                        $attachments = WWW_ROOT . $viewVars["docs"];
                                        $mailSent = $this->Common->sendEmail($to, $subject, $template, $viewVars, $cc = false, $bcc = false, $attachments);
                                    } else {
                                        $mailSent = $this->Common->sendEmail($to, $subject, $template, $viewVars, $cc = false, $bcc = false, $attachments = false);
                                    }
                                } else {
                                    echo "Invalid Email : " . $data['Email']['to'];
                                    echo "<hr>";

                                    $message = $data['Email']['id'] . " - to : " . $to . " : Email cron - Invalid email";
                                    $this->log($message, "email");

                                    $mailSent = true;
                                }
                            }
                        }
                    }


                    echo "<hr>";

                    if ($mailSent) {
                        $this->Archive->updateAll(array('mail_sent' => 1), array('email_id' => $data['Email']['id']));
                    } else {
                        $message = $data['Email']['id'] . " - to : " . $to . " : Email cron - mail not sent";
                        $this->log($message, "email");
                    }
                }
            }

            echo "Email sent successfully.";
            exit;
        } else {
            echo "No email is pending in queue.";
            exit;
        }
    }

    public function globalemailcron() {
        $this->loadModel("globalemail");
        $this->loadModel("Announcement");
        $params['conditions'] = array("scheduled_time <= " => date("Y-m-d H:i:s", time()));
//        $params['conditions'] = array();
        $globaldata = $this->globalemail->find("all", $params);
//        echo $this->globalemail->getLastQuery();
//        pr($globaldata);
//        exit;
        $image_attached = "";
        foreach ($globaldata as $globalemailsdata) {
//            pr($globalemailsdata["globalemail"]['announcement_id']); exit;
            if (filter_var($globalemailsdata['globalemail']['to'], FILTER_VALIDATE_EMAIL)) {
                echo $to = $globalemailsdata['globalemail']['to'];
//exit;
                $subject = $globalemailsdata['globalemail']['subject'];
                $viewVars = unserialize($globalemailsdata["globalemail"]['config_vars']);

                $user_id = $viewVars["userid"];
                $template = $globalemailsdata['globalemail']['template'];
                $attatched = 0;
                $attachment = 0;
                $attachmenttosend = "";

                if (isset($viewVars["attached"]) && $viewVars["attached"] != "") {
                    $attachmenttosend = WWW_ROOT . "attachmentimages/" . $viewVars["attached"];
                    $image_attached = $attachmenttosend;
                }

                if ($viewVars["for"] != "tandc") {
                    if ($attachmenttosend != "" && file_exists(WWW_ROOT . "attachmentimages/" . $viewVars["attached"])) {
                        $attachedline = "*This notification comes with an email attachment";
                        $viewVars["msg"] = $viewVars["msg"] . "<br><br>" . $attachedline;
                        // $mailSent = $this->Common->sendEmail($to, $subject, $template, $viewVars, $cc = false, $bcc = false, $attachmenttosend);
                    } else {
                      //  $mailSent = $this->Common->sendEmail($to, $subject, $template, $viewVars, $cc = false, $bcc = false);
                        $mailSent = true;
                    }
                } else {
                    $mailSent = true;
                }

                if ($viewVars["for"] == "tandc") {
                    $viewVars["msg"] = "nDorse LLC has updated the Terms and Conditions, please go through it once again.";
                    $deviceToken_msg_arr = $this->Common->sendpushnotiforglobalsettings($viewVars["userid"], $viewVars["msg"]);
                } else {
                    //Commented intensionally to stop multiple tray notifications @08/07/2017 by Babulal Prasad
                    // $deviceToken_msg_arr = $this->Common->sendpushnotiforglobalsettings($viewVars["userid"], $viewVars["msg"]);
                }
            } else {
                echo "Invalid Email : " . $globalemailsdata['globalemail']['to'];
                echo "<hr>";
                $message = $globalemailsdata['globalemail']['id'] . " - to : " . $to . " : Email cron - Invalid email";
                $this->log($message, "email");
                $mailSent = true;
            }
            echo "<hr>";
//echo $mailSent;
//exit;
            if ($mailSent) {
                $this->globalemail->id = $globalemailsdata['globalemail']['id'];
                $this->Announcement->id = $globalemailsdata["globalemail"]['announcement_id'];
                $this->Announcement->save(array('status' => 'inactive'), array('id' => $globalemailsdata["globalemail"]['announcement_id']));
                $this->globalemail->delete();
            } else {
                $message = $globalemailsdata['globalemail']['id'] . " - to : " . $to . " : Email cron - mail not sent";
                //   $this->log($message, "email");
            }


            if ($globalemailsdata['globalemail']['scheduled_time'] > 0) {
                $deviceToken_msg_arr = $this->Common->sendpushnotiforglobalsettings($viewVars["userid"], $viewVars["msg"]);
                $this->Announcement->id = $globalemailsdata["globalemail"]['announcement_id'];
                $this->Announcement->save(array('status' => 'inactive'), array('id' => $globalemailsdata["globalemail"]['announcement_id']));
//                $this->Announcement->save(array('status' => 'active'), array('id' => '1'));
            }
        }
        if ($image_attached != "") {
            unlink($image_attached);
        }
        exit;
    }

    /* Added by Babulal Prasad @06-jan-2018 
     * To send scheduled Announcements 
     */

    public function globalscheduledemailcron() {
        $this->loadModel("globalemail");
        $params['conditions'] = array("scheduled_time <= " => date("Y-m-d H:i:s", time()));
        $globaldata = $this->globalemail->find("all", $params); //// Put check for scheduled 
        echo $this->globalemail->getLastQuery();
        exit;
        //print_r($globaldata);
        $image_attached = "";
        foreach ($globaldata as $globalemailsdata) {
            if (filter_var($globalemailsdata['globalemail']['to'], FILTER_VALIDATE_EMAIL)) {
                echo $to = $globalemailsdata['globalemail']['to'];
                $subject = $globalemailsdata['globalemail']['subject'];
                $viewVars = unserialize($globalemailsdata["globalemail"]['config_vars']);

                $user_id = $viewVars["userid"];
                $template = $globalemailsdata['globalemail']['template'];
                $attatched = 0;
                $attachment = 0;
                $attachmenttosend = "";

                if (isset($viewVars["attached"]) && $viewVars["attached"] != "") {
                    $attachmenttosend = WWW_ROOT . "attachmentimages/" . $viewVars["attached"];
                    $image_attached = $attachmenttosend;
                }

                if ($viewVars["for"] != "tandc") {
                    if ($attachmenttosend != "" && file_exists(WWW_ROOT . "attachmentimages/" . $viewVars["attached"])) {
                        $attachedline = "*This notification comes with an email attachment";
                        $viewVars["msg"] = $viewVars["msg"] . "<br><br>" . $attachedline;
                        $mailSent = $this->Common->sendEmail($to, $subject, $template, $viewVars, $cc = false, $bcc = false, $attachmenttosend);
                    } else {
                        //$mailSent = $this->Common->sendEmail($to, $subject, $template, $viewVars, $cc = false, $bcc = false);

                        $deviceToken_msg_arr = $this->sendpushnotiforglobalsettings($usersid, $content);

                        $mailSent = true;
                    }
                } else {
                    $mailSent = true;
                }

                if ($viewVars["for"] == "tandc") {
                    $viewVars["msg"] = "nDorse LLC has updated the Terms and Conditions, please go through it once again.";
                    $deviceToken_msg_arr = $this->Common->sendpushnotiforglobalsettings($viewVars["userid"], $viewVars["msg"]);
                } else {
                    //Commented intensionally to stop multiple tray notifications @08/07/2017 by Babulal Prasad
                    // $deviceToken_msg_arr = $this->Common->sendpushnotiforglobalsettings($viewVars["userid"], $viewVars["msg"]);
                }
            } else {
                echo "Invalid Email : " . $globalemailsdata['globalemail']['to'];
                echo "<hr>";
                $message = $globalemailsdata['globalemail']['id'] . " - to : " . $to . " : Email cron - Invalid email";
                $this->log($message, "email");
                $mailSent = true;
            }
            echo "<hr>";
            if ($mailSent) {
                $this->globalemail->id = $globalemailsdata['globalemail']['id'];
                $this->globalemail->delete();
            } else {
                $message = $globalemailsdata['globalemail']['id'] . " - to : " . $to . " : Email cron - mail not sent";
                $this->log($message, "email");
            }
        }
        if ($image_attached != "") {
            unlink($image_attached);
        }
        exit;
    }

    public function countingBadgesOnRecieve() {
        $trophy = $this->Trophy->find("first", array("conditions" => array("base_condition" => "count", "type" => "received")));
        $endorseCount = $trophy['Trophy']['base_value'];

        $newBadges = $this->Endorsement->query("
            SELECT  * ,  TRUNCATE( (IFNULL(endorsed_count,0) +  IFNULL(endorser_count,0)/10)  / 10, 0)  as badge_count  FROM
                                                (SELECT Endorsement.* , COUNT(Endorsement.id) as endorsed_count FROM endorsements AS Endorsement 
                                                GROUP BY  Endorsement.endorsed_id , Endorsement.organization_id) AS EndorseeEndorsement
                                                LEFT JOIN
                                                (SELECT Endorsement.* , COUNT(Endorsement.id) as endorser_count FROM endorsements AS Endorsement 
                                                GROUP BY  Endorsement.endorser_id , Endorsement.organization_id )AS EndorserEndorsement
                                                
                                                ON (EndorseeEndorsement.endorsed_id = EndorserEndorsement.endorser_id AND EndorseeEndorsement.organization_id = EndorserEndorsement.organization_id)
                                                
                                                LEFT JOIN users AS User ON (EndorseeEndorsement.endorsed_id = User.id)
                                                LEFT JOIN organizations AS Organization ON (EndorseeEndorsement.organization_id = Organization.id)
                                                LEFT JOIN badges AS Badge ON (EndorseeEndorsement.endorsed_id = Badge.user_id AND EndorseeEndorsement.organization_id = Badge.organization_id  AND Badge.trophy_id =  " . $trophy['Trophy']['id'] . ")
                                                
                                            WHERE
                                                 (TRUNCATE((IFNULL(endorsed_count,0) +  IFNULL(endorser_count,0)/10)  / 10 ,0)> Badge.count)
                                                    OR  (Badge.trophy_id IS NULL AND IFNULL(endorsed_count,0) +  TRUNCATE(IFNULL(endorser_count,0)/10, 0) >= 10) 
                                                ORDER BY EndorseeEndorsement.endorsed_id ASC

                ");
//        pr($newBadges);die;

        $emailQueue = array();
        $saveBadges = array();
        $subject = "nDorse notification -- Badge or trophy received";
        $configVars = array('trophy_name' => $trophy['Trophy']['name'], 'trophy_image' => $trophy['Trophy']['image']);
        foreach ($newBadges as $newBadge) {
            $badge = array();
//            $badge['count'] = 
            if (empty($newBadge['Badge']['id'])) {
                $badge['user_id'] = $newBadge['EndorseeEndorsement']['endorsed_id'];
                $badge['organization_id'] = $newBadge['EndorseeEndorsement']['organization_id'];
                $badge['trophy_id'] = $trophy['Trophy']['id'];
                $badge['count'] = 1;
            } else {
                $badge = $newBadge['Badge'];
                $badge['count'] += 1;
            }

            $configVars['first_name'] = $newBadge['User']['fname'];
            $configVars['org_name'] = $newBadge['Organization']['name'];

            /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
            $userIdEncrypted = base64_encode($newBadge['User']['id']);
            $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
            $configVars["pathToRender"] = $pathToRender;
            /*             * * */


            $saveBadges[] = $badge;

            if (!empty($newBadge['User']['email'])) {
                $emailQueue[] = array("to" => $newBadge['User']['email'], "subject" => $subject, "config_vars" => serialize($configVars), "template" => "badge_receive");
            }
        }


        if (!empty($saveBadges)) {
            $this->Badge->saveMany($saveBadges);
            $this->Email->saveMany($emailQueue);
            echo "Badges awarded";
        } else {
            echo "No badges in queue";
        }
        exit;
    }

    public function durationBadgeRecieve() {
        $trophy = $this->Trophy->find("first", array("conditions" => array("base_condition" => "duration", "type" => "received")));
        $endorseDuration = $trophy['Trophy']['base_value'];
        $todayDay = date("d");
        $lastDay = date("t");
        $month = date("n", strtotime("first day of last month"));

        if ($todayDay == $lastDay || 1) {
            $topEndorsedPeople = $this->Endorsement->query("
            SELECT * FROM
                (
                    SELECT MONTH(Badge.updated) as last_badge_update, User.email , User.fname, Organization.name as orgName,  COUNT(Endorsement.id) as endorsement_count,
                    Endorsement.* , Badge.id as badge_id, Badge.user_id as badge_user_id,  Badge.trophy_id, Badge.count as badge_count
                    FROM endorsements AS Endorsement 
                    LEFT JOIN users AS User ON (Endorsement.endorsed_id = User.id) 
                    LEFT JOIN organizations AS Organization ON (Endorsement.organization_id = Organization.id) 
                    LEFT JOIN badges AS Badge ON (Endorsement.endorsed_id = Badge.user_id AND Endorsement.organization_id = Badge.organization_id AND Badge.trophy_id = " . $trophy['Trophy']['id'] . ")
                    WHERE MONTH(Endorsement.created) = " . $month . " AND (Badge.updated IS NULL OR MONTH(Badge.updated) != " . date("n") . ") 
                    GROUP BY Endorsement.endorsed_id , Endorsement.organization_id
                ) AS EndorseTop
                LEFT JOIN 
                ( 
                  SELECT MAX(endorsement_count)  as max_endorsement_count , EndorseNested.organization_id , EndorseNested.endorsed_id FROM 
                   ( 
                     SELECT COUNT(EndorsementInner.id) endorsement_count  , EndorsementInner.endorsed_id , EndorsementInner.organization_id
                     FROM endorsements as EndorsementInner 
                     WHERE MONTH(EndorsementInner.created) = " . $month . "
                     GROUP BY EndorsementInner.endorsed_id , EndorsementInner.organization_id 
                   ) AS EndorseNested GROUP BY EndorseNested.organization_id 
                )  AS EndorseMax
                ON EndorseTop.endorsement_count  = EndorseMax.max_endorsement_count 
                WHERE EndorseTop.organization_id = EndorseMax.organization_id order by  EndorseTop.organization_id
											");


            $subject = "nDorse notification -- Badge or trophy received";
            $configVars = array('trophy_name' => $trophy['Trophy']['name'], 'trophy_image' => $trophy['Trophy']['image']);
            $emailQueue = array();
            $saveBadges = array();

            foreach ($topEndorsedPeople as $topEndorsed) {
                $badge = array();
                $badge['user_id'] = $topEndorsed['EndorseTop']['endorsed_id'];
                $badge['organization_id'] = $topEndorsed['EndorseTop']['organization_id'];
                $badge['trophy_id'] = $trophy['Trophy']['id'];
                if (empty($topEndorsed['EndorseTop']['badge_id'])) {

                    $badge['count'] = 1;
                } else {
                    //if($topEndorsed[0]['last_badge_update'] == date("n")) {
                    //				continue;
                    //}
                    $badge['id'] = $topEndorsed['EndorseTop']['badge_id'];
                    $badge['count'] = $topEndorsed['EndorseTop']['badge_count'] + 1;
                }

                $saveBadges[] = $badge;
                if (!empty($topEndorsed['EndorseTop']['email'])) {
                    $configVars['fname'] = $topEndorsed['EndorseTop']['fname'];
                    $configVars['orgName'] = $topEndorsed['EndorseTop']['orgName'];


                    /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                    $userIdEncrypted = base64_encode($topEndorsed['EndorseTop']['endorsed_id']);
                    $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                    $configVars["pathToRender"] = $pathToRender;
                    /*                     * * */


                    $configVars = serialize($configVars);
                    $emailQueue[] = array("to" => $topEndorsed['EndorseTop']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "count_badge_receive");
                }
            }
            if (!empty($saveBadges)) {
                $this->Badge->saveMany($saveBadges);
                $this->Email->saveMany($emailQueue);
                echo "Badges awarded";
            } else {
                echo "No badges in queue";
            }
        } else {
            echo "Today is not end of the month";
        }

        exit;
    }

    public function durationBadgeSent() {
        $trophy = $this->Trophy->find("first", array("conditions" => array("base_condition" => "duration", "type" => "sent")));
        $endorseDuration = $trophy['Trophy']['base_value'];
        $todayDay = date("d");
        $lastDay = date("t");
        $month = date("n", strtotime("first day of last month"));

        if ($todayDay == $lastDay || 1) {

            $topEndorsers = $this->Endorsement->query("
                SELECT *  FROM
                (
                    SELECT MONTH(Badge.updated) as last_badge_update, User.email , User.fname, Organization.name as orgName, COUNT(Endorsement.id) as endorsement_count,
                    Endorsement.* , Badge.id as badge_id, Badge.user_id as badge_user_id,  Badge.trophy_id, Badge.count as badge_count
                    FROM endorsements AS Endorsement 
                    LEFT JOIN users AS User ON (Endorsement.endorser_id = User.id) 
                    LEFT JOIN organizations AS Organization ON (Endorsement.organization_id = Organization.id) 
                    LEFT JOIN badges AS Badge ON (Endorsement.endorser_id = Badge.user_id AND Endorsement.organization_id = Badge.organization_id AND Badge.trophy_id = " . $trophy['Trophy']['id'] . ") 
                    WHERE MONTH(Endorsement.created) = " . $month . "
                    AND (Badge.updated IS NULL OR MONTH(Badge.updated) != " . date("n") . ")
                    GROUP BY Endorsement.endorser_id , Endorsement.organization_id ) AS EndorseTop
                    LEFT JOIN 
                    (
                        SELECT MAX(endorsement_count)  as max_endorsement_count, EndorseNested.organization_id , EndorseNested.endorser_id FROM 
                        (
                            SELECT COUNT(EndorsementInner.id) endorsement_count , EndorsementInner.endorser_id , EndorsementInner.organization_id
                            FROM endorsements as EndorsementInner 
                            WHERE MONTH(EndorsementInner.created)  = " . $month . "
                            GROUP BY EndorsementInner.endorser_id , EndorsementInner.organization_id 
                        ) AS EndorseNested GROUP BY EndorseNested.organization_id 
                    ) AS EndorseMax ON EndorseTop.endorsement_count  = EndorseMax.max_endorsement_count 
                    WHERE EndorseTop.organization_id = EndorseMax.organization_id order by  EndorseTop.organization_id
            ");
            //echo $this->Endorsement->getLastQuery();die;
//           pr($topEndorsers);die;

            $subject = "You are awarded with " . $trophy['Trophy']['name'];
            $configVars = array('trophy_name' => $trophy['Trophy']['name'], 'trophy_image' => $trophy['Trophy']['image']);

            foreach ($topEndorsers as $topEndorser) {
                $badge = array();
                $badge['user_id'] = $topEndorser['EndorseTop']['endorser_id'];
                $badge['organization_id'] = $topEndorser['EndorseTop']['organization_id'];
                $badge['trophy_id'] = $trophy['Trophy']['id'];

                if (empty($topEndorser['EndorseTop']['badge_id'])) {

                    $badge['count'] = 1;
                } else {
                    //if($topEndorsed[0]['last_badge_update'] == date("n")) {
                    //				continue;
                    //}
                    $badge['id'] = $topEndorser['EndorseTop']['badge_id'];
                    $badge['count'] = $topEndorser['EndorseTop']['badge_count'] + 1;
                }

                $saveBadges[] = $badge;

                if (!empty($topEndorser['EndorseTop']['email'])) {
                    $configVars['fname'] = $topEndorser['EndorseTop']['fname'];
                    $configVars['orgName'] = $topEndorser['EndorseTop']['orgName'];

                    /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                    $userIdEncrypted = base64_encode($topEndorsed['EndorseTop']['endorsed_id']);
                    $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                    $configVars["pathToRender"] = $pathToRender;
                    /*                     * * */

                    $configVars = serialize($configVars);
                    $emailQueue[] = array("to" => $topEndorser['EndorseTop']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "count_badge_receive");
                }
            }
            if (!empty($saveBadges)) {
                $this->Badge->saveMany($saveBadges);
                $this->Email->saveMany($emailQueue);
                echo "Badges awarded";
            } else {
                echo "No badges in queue";
            }
        } else {
            echo "Today is not end of the month";
        }

        exit;
    }

    public function errorEmailCron() {
        ini_set('memory_limit', '2G');
        $params = array(
            'fields' => array(),
            'conditions' => array('mail_sent' => 0),
            'order' => array('ErrorEmail.created ASC'),
            'limit' => 50
        );

        $emailData = $this->ErrorEmail->find('all', $params);

        if (!empty($emailData)) {
            foreach ($emailData as $data) {
                $this->log($data, "registeremaillogs");

                echo $to = $data['ErrorEmail']['to'];
                echo "<hr>";
                $subject = $data['ErrorEmail']['subject'];
                $viewVars = unserialize($data['ErrorEmail']['config_vars']);
                $template = $data['ErrorEmail']['template'];
                $bcc = isset($data['ErrorEmail']['bcc']) ? $data['ErrorEmail']['bcc'] : array();
                $cc = isset($data['ErrorEmail']['cc']) ? $data['ErrorEmail']['cc'] : array();

                if ($this->Common->sendEmail($to, $subject, $template, $viewVars, $cc = false, $bcc = false, $attachments = false)) {
                    $this->ErrorEmail->updateAll(array('mail_sent' => 1), array('id' => $data['ErrorEmail']['id']));
                } else {
                    $message = $data['ErrorEmail']['id'] . " - to : " . $to . " : errorEmailCron cron - mail not sent";
                    $this->log($message, "email");
                }
            }

            echo "Email sent successfully.";
            exit;
        } else {
            echo "No email is pending in queue.";
            exit;
        }
    }

    public function errorEmailCronDisplay() {
        ini_set('memory_limit', '2G');
        $params = array(
            'fields' => array(),
            'conditions' => array('mail_sent' => 0),
            'order' => array('ErrorEmail.created ASC'),
            'limit' => 50
        );

        $emailData = $this->ErrorEmail->find('all', $params);

        if (!empty($emailData)) {
            foreach ($emailData as $data) {
                $this->log($data, "registeremaillogs");

                echo $to = $data['ErrorEmail']['to'];
                echo "<hr>";
                $subject = $data['ErrorEmail']['subject'];
                $viewVars = unserialize($data['ErrorEmail']['config_vars']);
                $template = $data['ErrorEmail']['template'];
                $bcc = isset($data['ErrorEmail']['bcc']) ? $data['ErrorEmail']['bcc'] : array();
                $cc = isset($data['ErrorEmail']['cc']) ? $data['ErrorEmail']['cc'] : array();
                //pr($viewVars) ;die;
                echo 'Code' .
                '<br>' .
                $viewVars['error'] . " (" . $viewVars['code'] . ")" .
                '<br>' .
                '<br>' .
                'Description:' .
                '<br>' .
                $viewVars['description'] .
                '<br>' .
                '<br>' .
                'File:' .
                '<br>' .
                $viewVars['file'] .
                '<br>' .
                '<br>' .
                'Line:' .
                '<br>' .
                $viewVars['line'] .
                '<br>' .
                '<br>' .
                'Context:' .
                '<br>';
                echo "<pre>";
                print_r($viewVars['context']);
                echo "</pre>";
                echo '<br>' .
                '<br>' .
                'Session:' .
                '<br>';
                echo "<pre>";
                print_r($viewVars['session']);
                echo "</pre>";
                echo '<br>' .
                '<br>' .
                'Server:' .
                '<br>';
                echo "<pre>";
                print_r($viewVars['server']);
                echo "</pre>";
                echo '<br>' .
                '<br>' .
                'request:' .
                '<br>';
                echo "<pre>";
                print_r($viewVars['request']);
                echo "</pre>";
                echo '<br>' .
                '<br>' .
                'Trace:' .
                '<br>';
                echo "<pre>";
                print_r($viewVars['trace']);
                echo "</pre>";
                echo '<br>' .
                '<hr>';

                //if ($this->Common->sendEmail($to, $subject, $template, $viewVars, $cc = false, $bcc = false, $attachments = false)) {
                //    $this->ErrorEmail->updateAll(array('mail_sent' => 1), array('id' => $data['ErrorEmail']['id']));
                //} else {
                //    $message = $data['ErrorEmail']['id'] . " - to : " . $to . " : errorEmailCron cron - mail not sent";
                //    $this->log($message, "email");
                //}
            }

            echo "Email sent successfully.";
            exit;
        } else {
            echo "No email is pending in queue.";
            exit;
        }
    }

    public function endorseEmails_notInUse() {
        ini_set('memory_limit', '2G');

        //$params = array();
        //$params['fields'] = array("*", "COUNT(Endorsement.id) as count");
        ////$params['order'] = 
        //$params['conditions']	= array('endorsement_for' => 'user');
        //$params['group'] = "Endorsement.endorsed_id, Endorsement.organization_id, email_sent ";
        ////$params['joins'] = array(
        ////																		array(
        ////																						'table' => 'org_core_values',
        ////																						'alias' => 'OrgCoreValues',
        ////																						'type' => 'LEFT',
        ////																						'conditions' => array(
        ////																										'Endorsement.organization_id = OrgCoreValues.organization_id',
        ////																						)
        ////																		));
        ////$params['group'] = "OrgCoreValues.id";
        //
								//
								//$this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies', "EndorseCoreValues")));
        //
								//$firstEndorsedUsers = $this->Endorsement->find("all", $params);

        $firstEndorserUsers = $this->Endorsement->query("SELECT * FROM endorsements as Endorsement
                                                        LEFT JOIN users as Endorser ON (Endorsement.endorser_id = Endorser.id)
                                                        WHERE
                                                        Endorsement.id IN (SELECT min(id) FROM endorsements GROUP BY endorser_id, organization_id) AND Endorsement.email_sent = 0
                                                    ");

        foreach ($firstEndorserUsers as $endorsement) {
            $endorseCoreValues = array();
            //foreach ($endorsement['EndorseCoreValues'] as $endorseCoreValue) {
            //				$endorseCoreValues[] = $orgCoreValues[$endorsement['id']];
            //}

            $configVars = array(
            );
            $template = "endorser_first";
            $subject = 'First nDorsement';

            $configVars["first_name"] = $endorsement['Endorser']['fname'];

            /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
            $userIdEncrypted = base64_encode($endorsement['Endorser']['id']);
            $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
            $configVars["pathToRender"] = $pathToRender;
            /*             * ** */


            $to = $endorsement['Endorser']['email'];
            if (!$this->Common->sendEmail($to, $subject, $template, $configVars)) {
                $message = $to . " : endorseEmails first endorsement cron - mail not sent";
                $this->log($message, "email");
            }
        }

        $firstEndorsedUsers = $this->Endorsement->query("SELECT * FROM endorsements as Endorsement
                                                        LEFT JOIN users as EndorsedUser ON (Endorsement.endorsed_id = EndorsedUser.id)
                                                        LEFT JOIN users as Endorser ON (Endorsement.endorser_id = Endorser.id)
                                                        WHERE Endorsement.id IN ( SELECT min(id) FROM endorsements GROUP BY endorsed_id, organization_id )
                                                        AND Endorsement.email_sent = 0 AND Endorsement.endorsement_for = 'user'
                                                        ");

        $firstEndorsedIds = array();

        foreach ($firstEndorsedUsers as $firstEndorsement) {
            $firstEndorsedIds[] = $firstEndorsement['Endorsement']['id'];
        }

        $params = array();
        $params['fields'] = array("*");
        $params['joins'] = array(
            array(
                'table' => 'org_core_values',
                'alias' => 'OrgCoreValues',
                'type' => 'LEFT',
                'conditions' => array(
                    'EndorseCoreValue.value_id = OrgCoreValues.id',
                )
        ));
        $params['group'] = "OrgCoreValues.id";
        $params['conditions'] = array("endorsement_id" => $firstEndorsedIds);

        $endorsementValues = $this->EndorseCoreValue->find("all", $params);
        //echo $this->EndorseCoreValue->getLastQuery();
        //pr($firstEndorsedUsers);die;

        $orgCoreValues = array();



        foreach ($endorsementValues as $endorsementValue) {
            if (!isset($orgCoreValues[$endorsementValue['EndorseCoreValue']['endorsement_id']])) {
                $orgCoreValues[$endorsementValue['EndorseCoreValue']['endorsement_id']] = array();
            }
            $orgCoreValues[$endorsementValue['EndorseCoreValue']['endorsement_id']][] = $endorsementValue['OrgCoreValues']['name'];
        }

        //pr($endorsementValues);
        //pr($orgCoreValues);
        // pr($firstEndorsedUsers);die;
        $deviceToken_ios_msg_arr = array();
        foreach ($firstEndorsedUsers as $endorsement) {
            $endorseCoreValues = array();
            //foreach ($endorsement['EndorseCoreValues'] as $endorseCoreValue) {
            //				$endorseCoreValues[] = $orgCoreValues[$endorsement['id']];
            //}
            $organization_id = $endorsement["Endorsement"]["organization_id"];
            $user_id = $endorsement["EndorsedUser"]["id"];
            $endorsedUsersorg = $this->UserOrganization->find("all", array(
                "joins" => array(
                    array('table' => "login_statistics",
                        "alias" => "LoginStatistics",
                        "type" => "LEFT",
                        'conditions' => array(
                            'LoginStatistics.user_id =User.id AND LoginStatistics.live =1'
                        )
                    )
                ),
                "conditions" => array("organization_id" => $organization_id, "UserOrganization.status" => 1, "UserOrganization.user_id" => $user_id),
                "fields" => array("UserOrganization.id", "LoginStatistics.*", "Organization.name")));

            if (!empty($endorsedUsersorg)) {

                $configVars = array(
                    "endorser_name" => $endorsement['Endorser']['fname'] . " " . $endorsement['Endorser']['lname']
                );
                $template = "endorse";
                $subject = 'First nDorsement Received';

                $configVars['for'] = "user";
                $configVars['type'] = "first";
                $configVars["first_name"] = $endorsement['EndorsedUser']['fname'];
                $configVars['core_values'] = $orgCoreValues[$endorsement['Endorsement']['id']];

                $to = $endorsement['EndorsedUser']['email'];
                if ($this->Common->sendEmail($to, $subject, $template, $configVars)) {
                    $this->Endorsement->updateAll(array('email_sent' => 1), array('id' => $endorsement['Endorsement']['id']));
                } else {
                    $this->Endorsement->updateAll(array('email_sent' => 4), array('id' => $endorsement['Endorsement']['id']));
                }
                if (!empty($endorsedUsersorg[0]["LoginStatistics"]) && $endorsedUsersorg[0]["LoginStatistics"]["device_id"] != "") {

                    if (strtolower($endorsedUsersorg[0]["LoginStatistics"]["os"]) == "ios") {
                        //print_r($device_token);
                        $deviceToken_msg_arr = array();
                        $token = $endorsedUsersorg[0]["LoginStatistics"]["device_id"];
                        $count = 1;
                        $end_name = $endorsement['Endorser']['fname'] . " " . $endorsement['Endorser']['lname'];
                        if ($endorsement["Endorsement"]["type"] == "anonymous") {
                            $end_name = "anonymously";
                        }
                        $msg = 'Hi ' . ucfirst($endorsement['EndorsedUser']['fname']) . ", Congratulations on your first nDorsement by " . $end_name . " from " . $endorsedUsersorg[0]["Organization"]["name"] . "!!";

                        $parameter = array("org_id" => $organization_id, "category" => "SwitchAction", "notification_type" => "post_promotion",
                            "title" => "nDorse App");

                        $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
                        //print_r($deviceToken_msg_arr);
//                        echo $this->Common->sendPushNotification($deviceToken_msg_arr);
                    } elseif (strtolower($endorsedUsersorg[0]["LoginStatistics"]["os"]) == "android") {

                        $deviceToken_msg_arr = array();
                        $token = $endorsedUsersorg[0]["LoginStatistics"]["device_id"];
                        $count = 1;
                        $end_name = $endorsement['Endorser']['fname'] . " " . $endorsement['Endorser']['lname'];
                        if ($endorsement["Endorsement"]["type"] == "anonymous") {
                            $end_name = "anonymously";
                        }
                        $msg = 'Hi ' . ucfirst($endorsement['EndorsedUser']['fname']) . ", Congratulations on your first nDorsement by " . $end_name . " from " . $endorsedUsersorg[0]["Organization"]["name"] . "!!";

                        $parameter = array("org_id" => $organization_id, "category" => "SwitchAction", "notification_type" => "post_promotion",
                            "title" => "nDorse App");

                        $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
                        //print_r($deviceToken_msg_arr);
//                        echo $this->Common->sendPushNotificationAndroid($deviceToken_msg_arr);
                    }
                }
            }




            //	foreach($endorsedUsers as $endorsedUser) {
            //	if(isset($endorsedUser["LoginStatistics"]) && $endorsedUser["LoginStatistics"]["os"]=="ios"){
            //		//print_r($device_token);
            //		//$deviceToken_msg_arr = array();
            //		$token = $endorsedUser["LoginStatistics"]["device_id"];
            //        $count = 1;
            //        $msg = 'Hi '.$endorsedUser['User']['fname']." , you have endorsed by entity";
            //        $deviceToken_ios_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg);
            //		//print_r($deviceToken_msg_arr);
            //	   // $this->Common->sendPushNotification($deviceToken_ios_msg_arr);
            //	}elseif(isset($endorsedUser["LoginStatistics"]) && $endorsedUser["LoginStatistics"]["os"]=="android"){
            //	}
            //}
        }
        //if(!empty($deviceToken_ios_msg_arr)){
        //												$this->Common->sendPushNotification($deviceToken_ios_msg_arr);				
        //								}
        //die;

        $params = array();
        $params['fields'] = array("*");
        $params['conditions'] = array('email_sent' => 0);
        $params['order'] = array('Endorsement.created ASC');

        $params['joins'] = array(
            array(
                'table' => 'users',
                'alias' => 'EndorsedUser',
                'type' => 'LEFT',
                'conditions' => array(
                    'Endorsement.endorsed_id = EndorsedUser.id',
                    'Endorsement.endorsement_for = "user"'
                )
            ),
            array(
                'table' => 'org_departments',
                'alias' => 'EndorsedDept',
                'type' => 'LEFT',
                'conditions' => array(
                    'Endorsement.endorsed_id = EndorsedDept.id',
                    'Endorsement.endorsement_for = "department"'
                )
            ),
            array(
                'table' => 'entities',
                'alias' => 'EndorsedEntity',
                'type' => 'LEFT',
                'conditions' => array(
                    'Endorsement.endorsed_id = EndorsedEntity.id',
                    'Endorsement.endorsement_for = "entity"'
                )
            ),
            array(
                'table' => 'users',
                'alias' => 'Endorser',
                'type' => 'LEFT',
                'conditions' => array(
                    'Endorsement.endorser_id = Endorser.id',
                )
            )
        );

        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseReplies')));

        $endorsements = $this->Endorsement->find('all', $params);
        //echo $this->Endorsement->getLastQuery();
        //pr($endorsements);die;

        $subject = "nDorsement Notification";
        $template = "endorse";

        foreach ($endorsements as $endorsement) {
            //$endorseCoreValues = array();
            //
												//foreach ($endorsement['EndorseCoreValues'] as $endorseCoreValue) {
            //				$endorseCoreValues[] = $orgCoreValues[$endorseCoreValue['value_id']];
            //}

            $configVars = array(
                "endorser_name" => $endorsement['Endorser']['fname'] . " " . $endorsement['Endorser']['lname']
            );

            $mailSent = false;

//            switch ($endorsement['Endorsement']['endorsement_for']) {
//                case "user" :
            //$subject = 'You are endorsed';

            $configVars['for'] = "user";
            $configVars["first_name"] = $endorsement['EndorsedUser']['fname'];

            $to = $endorsement['EndorsedUser']['email'];
            $endorserid = $endorsement['EndorsedUser']['id'];

            $endorsedUsersorg = $this->UserOrganization->find("all", array(
                "joins" => array(
                    array('table' => "login_statistics",
                        "alias" => "LoginStatistics",
                        "type" => "LEFT",
                        'conditions' => array(
                            'LoginStatistics.user_id =User.id AND LoginStatistics.live =1'
                        )
                    )
                ),
                "conditions" => array("organization_id" => $endorsement['Endorsement']["organization_id"], "UserOrganization.status" => 1, "UserOrganization.user_id" => $endorserid),
                "fields" => array("UserOrganization.id", "LoginStatistics.*", "Organization.name")));
            //print_r($endorsedUsersorg);exit;
            // $device_token = $this->LoginStatistics->find("first", array("conditions" => array("user_id" => $endorserid, "live" => 1)));

            if (!empty($endorsedUsersorg[0]["LoginStatistics"]) && $endorsedUsersorg[0]["LoginStatistics"]["device_id"] != "") {
                $deviceToken_msg_arr = array();
                $token = $endorsedUsersorg[0]["LoginStatistics"]["device_id"];
                $count = 1;
                $end_name = $endorsement['Endorser']['fname'] . " " . $endorsement['Endorser']['lname'];
                if ($endorsement["Endorsement"]["type"] == "anonymous") {
                    $end_name = "anonymously";
                }
                //Hi <endorsed name>,  You were ndorsed by <endorser name> from <organizaion name>. 
                $msg = 'Hi ' . ucfirst($endorsement['EndorsedUser']['fname']) . ", You were nDorsed by " . $end_name . " from " . $endorsedUsersorg[0]["Organization"]["name"] . ".";
                $parameter = array("org_id" => $endorsement['Endorsement']["organization_id"], "category" => "SwitchAction", "notification_type" => "post_promotion",
                    "title" => "nDorse App");

                $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
                if (strtolower($endorsedUsersorg[0]["LoginStatistics"]["os"]) == "ios") {
                    //print_r($device_token);
                    //print_r($deviceToken_msg_arr);
                    echo $this->Common->sendPushNotification($deviceToken_msg_arr);
                } elseif (strtolower($endorsedUsersorg[0]["LoginStatistics"]["os"]) == "android") {
                    echo $this->Common->sendPushNotificationAndroid($deviceToken_msg_arr);
                }
            }

            // send push notification
            //$this->Common->sendPushNotification($deviceToken_msg_arr);
            // end
            if (!empty($endorsedUsersorg)) {
                if ($this->Common->sendEmail($to, $subject, $template, $configVars)) {
                    $mailSent = true;
                }
            }
//                    break;
//
//                case "department" :
//                    //$subject = "Your department is endorsed";
//                    $configVars['for'] = "department";
//                    $departname = $configVars['endorsed_name'] = $endorsement['EndorsedDept']['name'];
//
//                    $array['joins'] = array(
//                        array(
//                            'table' => 'users',
//                            'alias' => 'User',
//                            'type' => 'INNER',
//                            'conditions' => array(
//                                'Organization.admin_id =User.id '
//                            )
//                        )
//                    );
//                    $endorsedUsers = $this->UserOrganization->find("all", array(
//                                "joins" => array(
//                                    array('table' => "login_statistics",
//                                        "alias" => "LoginStatistics",
//                                        "type" => "LEFT",
//                                        'conditions' => array(
//                                            'LoginStatistics.user_id =User.id AND LoginStatistics.live =1'
//                                        )
//                                    )
//                                ),
//                                "conditions" => array("department_id" => $endorsement['Endorsement']['endorsed_id'], "UserOrganization.status" => 1),
//                                "fields" => array("User.email", "User.fname", "User.lname", "LoginStatistics.*", "Organization.name")));
//
//                    //echo $this->UserOrganization->getLastQuery();die;
//                    //print_r($endorsedUsers);exit;if(!empty($device_token) && $device_token["LoginStatistics"]["device_id"]!=""){
//                    $deviceToken_ios_msg_arr = array();
//                    $end_name = $endorsement['Endorser']['fname'] . " " . $endorsement['Endorser']['lname'];
//                    if ($endorsement["Endorsement"]["type"] == "anonymous") {
//                        $end_name = "anonymously";
//                    }
//                    foreach ($endorsedUsers as $endorsedUser) {
//                        $org_name = $endorsedUser["Organization"]["name"];
//                        if (isset($endorsedUser["LoginStatistics"]) && strtolower($endorsedUser["LoginStatistics"]["os"]) == "ios") {
//                            //print_r($device_token);
//                            //$deviceToken_msg_arr = array();
//                            $token = $endorsedUser["LoginStatistics"]["device_id"];
//                            $count = 1;
//                            //Hi Amit, Marketing department is endorsed by Harish from Arcgate
//
//                            $msg = 'Hi ' . $endorsedUser['User']['fname'] . ", " . $departname . " department was nDorsed by " . $end_name . " from " . $org_name . ".";
//                            $parameter = array("org_id" => $endorsement['Endorsement']["organization_id"], "category" => "SwitchAction", "notification_type" => "post_promotion",
//                                "title" => "nDorse App");
//
//
//                            $deviceToken_ios_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
//                            //print_r($deviceToken_msg_arr);
//                            //  $this->Common->sendPushNotification($deviceToken_msg_arr);
//                        } elseif (isset($endorsedUser["LoginStatistics"]) && strtolower($endorsedUser["LoginStatistics"]["os"]) == "android") {
//                            $token = $endorsedUser["LoginStatistics"]["device_id"];
//                            $count = 1;
//                            //Hi Amit, Marketing department is endorsed by Harish from Arcgate
//
//                            $msg = 'Hi ' . $endorsedUser['User']['fname'] . ", " . $departname . " department was nDorsed by " . $end_name . " from " . $org_name . ".";
//                            $parameter = array("org_id" => $endorsement['Endorsement']["organization_id"], "category" => "SwitchAction", "notification_type" => "post_promotion",
//                                "title" => "nDorse App");
//
//
//                            $deviceToken_android_msg_arr = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
////                            $this->Common->sendPushNotificationAndroidcommon($deviceToken_android_msg_arr);
//                            // $this->Common->sendPushNotificationAndroid($deviceToken_android_msg_arr);
//                        }
//                    }
//                    if (!empty($deviceToken_ios_msg_arr)) {
////                        $this->Common->sendPushNotification($deviceToken_ios_msg_arr);
//                    }
//
//                    if (empty($endorsedUsers)) {
//                        $mailSent = true;
//                    } else {
//                        foreach ($endorsedUsers as $endorsedUser) {
//                            $configVars["first_name"] = $endorsedUser['User']['fname'];
//                            $to = $endorsedUser['User']['email'];
//                            if ($this->Common->sendEmail($to, $subject, $template, $configVars)) {
//                                $mailSent = true;
//                            }
//                        }
//                    }
//                    break;
//
//                case "entity" :
//                    //$subject = "Your sub organization is endorsed";
//                    $configVars['for'] = "entity";
//                    $endorseEntityName = $configVars['endorsed_name'] = $endorsement['EndorsedEntity']['name'];
//
//                    $endorsedUsers = $this->UserOrganization->find("all", array(
//                                "joins" => array(
//                                    array('table' => "login_statistics",
//                                        "alias" => "LoginStatistics",
//                                        "type" => "LEFT",
//                                        'conditions' => array(
//                                            'LoginStatistics.user_id =User.id AND LoginStatistics.live =1'
//                                        )
//                                    )
//                                ),
//                                "conditions" => array("entity_id" => $endorsement['Endorsement']['endorsed_id'], "UserOrganization.status" => 1),
//                                "fields" => array("User.email", "User.fname", "User.lname", "LoginStatistics.*", "Organization.name")));
//                    //echo $this->UserOrganization->getLastQuery();die;
//                    $end_name = $endorsement['Endorser']['fname'] . " " . $endorsement['Endorser']['lname'];
//                    if ($endorsement["Endorsement"]["type"] == "anonymous") {
//                        $end_name = "anonymously";
//                    }
//                    $deviceToken_ios_msg_arr = array();
//
//                    foreach ($endorsedUsers as $endorsedUser) {
//                        $org_name = $endorsedUser["Organization"]["name"];
//                        if (isset($endorsedUser["LoginStatistics"]) && strtolower($endorsedUser["LoginStatistics"]["os"]) == "ios") {
//                            //print_r($device_token);
//                            //$deviceToken_msg_arr = array();
//                            $token = $endorsedUser["LoginStatistics"]["device_id"];
//                            $count = 1;
//                            // $msg = 'Hi ' . $endorsedUser['User']['fname'] . " , you have endorsed by entity";
//
//                            $msg = 'Hi ' . $endorsedUser['User']['fname'] . ", the sub Organization " . $endorseEntityName . " was nDorsed by " . $end_name . " from " . $org_name . ".";
//                            //$deviceToken_ios_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg);
//                            $parameter = array("org_id" => $endorsement['Endorsement']["organization_id"], "category" => "SwitchAction", "notification_type" => "post_promotion",
//                                "title" => "nDorse App");
//
//
//                            $deviceToken_ios_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
//                            //print_r($deviceToken_msg_arr);
//                            // $this->Common->sendPushNotification($deviceToken_ios_msg_arr);
//                        } elseif (isset($endorsedUser["LoginStatistics"]) && strtolower($endorsedUser["LoginStatistics"]["os"]) == "android") {
//                            $token = trim($endorsedUser["LoginStatistics"]["device_id"]);
//                            $count = 1;
//                            // $msg = 'Hi ' . $endorsedUser['User']['fname'] . " , you have endorsed by entity";
//
//                            $msg = 'Hi ' . $endorsedUser['User']['fname'] . ", the sub Organization " . $endorseEntityName . " was nDorsed by " . $end_name . " from " . $org_name . ".";
//                            //$deviceToken_ios_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg);
//                            $parameter = array("org_id" => $endorsement['Endorsement']["organization_id"], "category" => "SwitchAction", "notification_type" => "post_promotion",
//                                "title" => "nDorse App");
//
//
//                            $deviceToken_android_msg_arr = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
//                            $this->Common->sendPushNotificationAndroidcommon($deviceToken_android_msg_arr);
//                            // $this->Common->sendPushNotificationAndroid($deviceToken_android_msg_arr);
//                        }
//                    }
//                    if (!empty($deviceToken_ios_msg_arr)) {
//                        $this->Common->sendPushNotification($deviceToken_ios_msg_arr);
//                    }
//                    if (empty($endorsedUsers)) {
//                        $mailSent = true;
//                    } else {
//                        foreach ($endorsedUsers as $endorsedUser) {
//                            $configVars["first_name"] = $endorsedUser['User']['fname'];
//                            $to = $endorsedUser['User']['email'];
//                            if ($this->Common->sendEmail($to, $subject, $template, $configVars)) {
//                                $mailSent = true;
//                            }
//                        }
//                    }
//
//                    break;
//            }


            if ($mailSent) {
                $this->Endorsement->updateAll(array('email_sent' => 1), array('id' => $endorsement['Endorsement']['id']));
            } else {
                $this->Endorsement->updateAll(array('email_sent' => 3), array('id' => $endorsement['Endorsement']['id']));
            }
        }

        echo "Email sent successfully.";
        exit;
    }

    public function verificationEmails() {
        $verifications = $this->Verification->find("all", array("conditions" => array('email_sent' => 0), 'limit' => 5));

        if (empty($verifications)) {
            echo "No pending emails in queue";
            exit;
        }

        $updateEmailIds = array();
        foreach ($verifications as $verification) {
            $updateEmailIds[] = $verification['Verification']['id'];
        }

        $mailSentUpdated = $this->Verification->updateAll(array('email_sent' => 2), array('id' => $updateEmailIds));

        if ($mailSentUpdated) {
            foreach ($verifications as $verification) {
                if (filter_var($verification['Verification']['email'], FILTER_VALIDATE_EMAIL)) {
                    $subject = "nDorse - Verify email";
                    $template = "verification";
                    $viewVars = array("verification_code" => $verification['Verification']['verification_code']);
                    echo $to = $verification['Verification']['email'];

                    $updated = $this->Verification->updateAll(array('email_sent' => 1), array('id' => $verification['Verification']['id']));
                    if ($updated) {
                        $vRecord = $this->Verification->find("first", array("conditions" => array('id' => $verification['Verification']['id'])));

                        if ($vRecord['Verification']['email_sent'] == 1) {
                            $this->Common->sendEmail($verification['Verification']['email'], $subject, $template, $viewVars);
                        }
                    }
                    //                if ($this->Common->sendEmail($verification['Verification']['email'], $subject, $template, $viewVars)) {
                    //                    $this->Verification->updateAll(array('email_sent' => 1), array('id' => $verification['Verification']['id']));
                    //                } else {
                    //                    $message = $verification['Verification']['id'] . " - to : " . $to . " : verificationEmails cron - mail not sent";
                    //                    $this->log($message, "email");
                    //                }
                } else {
                    $updated = $this->Verification->updateAll(array('email_sent' => 1), array('id' => $verification['Verification']['id']));
                    $message = $verification['Verification']['id'] . " - to : " . $verification['Verification']['email'] . " : verificationEmails cron - Invalid email";
                    $this->log($message, "email");
                }
            }
        }

        echo "Email sent successfully.";
        exit;
    }

    public function forgotPasswordEmails() {
        $params = array();
        $params['fields'] = array("*");
        $params['limit'] = 5;
        $params['conditions'] = array('email_sent' => 0);
        $params['joins'] = array(
            array(
                'table' => 'users',
                'alias' => 'User',
                'type' => 'LEFT',
                'conditions' => array(
                    'User.email = PasswordCode.email'
                )
            )
        );
        $verifications = $this->PasswordCode->find("all", $params);

        if (empty($verifications)) {
            echo "No pending emails in queue";
            exit;
        }

        $updateEmailIds = array();
        foreach ($verifications as $verification) {
            $updateEmailIds[] = $verification['PasswordCode']['id'];
        }

        $mailSentUpdated = $this->Verification->updateAll(array('email_sent' => 2), array('id' => $updateEmailIds));

        if ($mailSentUpdated) {
            foreach ($verifications as $verification) {
                if (filter_var($verification['PasswordCode']['email'], FILTER_VALIDATE_EMAIL)) {
                    $subject = "nDorse Password reset code";
                    $template = "forgot_password";
                    $viewVars = array("verification_code" => $verification['PasswordCode']['code'], "first_name" => $verification['User']['fname']);
                    echo $to = $verification['PasswordCode']['email'];
                    echo "<hr>";

                    $updated = $this->PasswordCode->updateAll(array('email_sent' => 1), array('id' => $verification['PasswordCode']['id']));
                    if ($updated) {
                        $vRecord = $this->PasswordCode->find("first", array("conditions" => array('id' => $verification['PasswordCode']['id'])));
                        if ($vRecord['PasswordCode']['email_sent'] == 1) {

                            /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                            $userIdEncrypted = base64_encode($verification['User']['id']);
                            $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                            $viewVars["pathToRender"] = $pathToRender;
                            /*                             * ** */

                            $this->Common->sendEmail($verification['PasswordCode']['email'], $subject, $template, $viewVars);
                        }
                    }
                    //                if ($this->Common->sendEmail($verification['PasswordCode']['email'], $subject, $template, $viewVars)) {
                    //                    $this->PasswordCode->updateAll(array('email_sent' => 1), array('id' => $verification['PasswordCode']['id']));
                    //                } else {
                    //                    $message = $verification['PasswordCode']['id'] . " - to : " . $to . " : forgotPasswordEmails cron - mail not sent";
                    //                    $this->log($message, "email");
                    //                }
                } else {
                    $updated = $this->PasswordCode->updateAll(array('email_sent' => 1), array('id' => $verification['PasswordCode']['id']));
                    $message = $verification['PasswordCode']['id'] . " - to : " . $verification['PasswordCode']['email'] . " : forgotPasswordEmails cron - Invalid email";
                    $this->log($message, "email");
                }
            }
        }

        echo "Email sent successfully.";
        exit;
    }

    public function orgadminaccessemail($data) {
        //print_r($data);

        $to = $data['Email']['to'];
        $subject = $data['Email']['subject'];

        $viewVars = unserialize($data['Email']['config_vars']);
        //print_r($viewVars);

        if (!isset($viewVars["org_id"])) {
            return true;
        }
        echo $user_id = $viewVars["user_id"];
        //

        $users = $this->UserOrganization->find("first", array("conditions" => array("user_id" => $user_id, "organization_id" => $viewVars["org_id"], "UserOrganization.status" => 1)));
        //print_r($users);
        if (!empty($users)) {
            $to = $users["User"]["email"];
        }

        //
        $array = array();
        $array['fields'] = array('Organization.id', 'Organization.name');
        $conditionarray = array();
        $conditionarray['Organization.id'] = $viewVars["org_id"];
        $userfname = $viewVars["fname"];
        //$array['joins'] = array(
        //    array(
        //        'table' => 'users',
        //        'alias' => 'User',
        //        'type' => 'INNER',
        //        'conditions' => array(
        //            'Organization.admin_id =User.id '
        //        )
        //    )
        //);
        $array['conditions'] = $conditionarray;
        $orgArray = $this->Organization->find("all", $array);

        //
        $userorgdata = $this->UserOrganization->find("all", array('joins' => array(array('table' => 'organizations', 'type' => 'INNER', 'conditions' => array('organizations.id = UserOrganization.organization_id'))), "conditions" => array("organization_id" => $viewVars["org_id"], "user_role" => 2, 'UserOrganization.status' => 1)));
        $adminorg = array();

        foreach ($userorgdata as $uservalorg) {
            if ($uservalorg["User"]["id"] > 0 && $uservalorg["User"]["email"] != $to) {
                $adminorg[] = $uservalorg;
            }
        }

        //
        // print_r($adminorg);

        $admin_email = "";
        $organization_name = "";
        if (!empty($orgArray)) {
            // $admin_email = $orgArray[0]["User"]["email"];
            // $admin_fname = $orgArray[0]["User"]["fname"];
            $organization_name = $orgArray[0]["Organization"]["name"];

            $roleactivity = "revoked from";
            $adminsubject = $subject;
            $usersubject = $subject;
            if ($viewVars["role"] == 2) {
                // $roleactivity = "granted to";
                $usersubject = "nDorse Notification -- Admin control granted";
                $viewVars["message"] = "This is to notify you that administrator control has been granted to the following username :" . $to . ". If you have not initiated that or it is not required then please contact nDorse team at support@nDorse.net.";
                $usermessage = "This is to notify you that administrator control has been granted to your following username : " . $to . " for " . $organization_name . " organization.";
            } else {
                $usersubject = "nDorse Notification -- Admin control revoked";
                $viewVars["message"] = "This is to notify you that administrator control has been revoked from the username:" . $to . ". If you have not initiated that or it is not required then please contact nDorse team at support@nDorse.net.";
                $usermessage = "This is to notify you that administrator control has been revoked from your username:" . $to .
                        ". If this was not expected then please contact the other administrators of the " . $organization_name . " organization.";
            }

            $template = $data['Email']['template'];
            $bcc = isset($data['Email']['bcc']) ? $data['Email']['bcc'] : array();
            $cc = isset($data['Email']['cc']) ? $data['Email']['cc'] : array();
            // send mail for org admin
            //echo $to="manishsharmabpr@gmail.com";
            //echo "<hr>";
            //echo $admin_email="msharma@arcgate.com";
            foreach ($adminorg as $admin) {

                $admin_email = $admin["User"]["email"];
                $admin_fname = $admin["User"]["fname"];
                //echo $admin_fname;
                //echo "<hr>";
                $viewVars["fname"] = $admin_fname;
                if ($this->Common->sendEmail($admin_email, $adminsubject, $template, $viewVars, $cc = false, $bcc = false, $attachments = false)) {
                    
                } else {
                    $message = $data['Email']['id'] . " - to : " . $admin_email . " : Email cron - mail not sent";
                    $this->log($message, "email");
                }
            }
            $viewVars["message"] = $usermessage;
            $viewVars["fname"] = $userfname;
            echo $to;
            echo "<hr>";
            if ($this->Common->sendEmail($to, $usersubject, $template, $viewVars, $cc = false, $bcc = false, $attachments = false)) {
                
            } else {
                $message = $data['Email']['id'] . " - to : " . $to . " : Email cron - mail not sent";
                $this->log($message, "email");
            }
        }
        return true;
    }

    public function orgadminaactionemail($data) {
        //print_r($data);
        $viewVars = unserialize($data['Email']['config_vars']);
        $org_id = $viewVars["org_id"];
        $org_name = $viewVars["name"];

        $to = $data['Email']['to'];
        $subject = $data['Email']['subject'];
        $template = $data['Email']['template'];
        $status = $viewVars["status"];
        $bcc = isset($data['Email']['bcc']) ? $data['Email']['bcc'] : array();
        $cc = isset($data['Email']['cc']) ? $data['Email']['cc'] : array();

        $viewVars = unserialize($data['Email']['config_vars']);
        $array = array();
        $array['fields'] = array('User.email', 'User.fname', 'User.lname', 'UserOrganization.status', 'UserOrganization.user_role');
        $conditionarray = array();
        $conditionarray['UserOrganization.organization_id'] = $org_id;
        $array['joins'] = array(
            array(
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => array(
                    'UserOrganization.user_id =User.id ',
                    'UserOrganization.status =1'
                )
            )
        );
        $array['conditions'] = $conditionarray;
        $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization', 'User')));
        $orgArray = $this->UserOrganization->find("all", $array);
        //print_r($orgArray);
        if ($status == 2) {
            $orgstatus = "deleted";
        } elseif ($status == 0) {
            $orgstatus = "deactivated";
        } elseif ($status == 1) {
            $orgstatus = "activated";
        }
        if (!empty($orgArray)) {
            foreach ($orgArray as $orgval) {

                $viewVars["fname"] = $orgval["User"]["fname"];
                if ($orgval["UserOrganization"]["user_role"] == 3) {
                    $viewVars["message"] = "This is to notify you that an administrator of " . $org_name . " has " . $orgstatus . " it. If it was not expected then please contact your organization's administrators.";
                } else {
                    // If you have not initiated that or it is not expected then please contact nDorse team at support@nDorse.net.
                    $addmsg = "required";
                    if ($status == 0) {
                        $addmsg = "expected";
                    }

                    $viewVars["message"] = "This is to notify you that an administrator of " . $org_name . " has " . $orgstatus . " it. If you have not initiated that or it is not " . $addmsg . " then please contact nDorse team at support@nDorse.net.";
                }
                // $orgval["User"]["email"] = "msharma@arcgate.com";
                echo "<hr>" . $orgval["User"]["email"] . "<hr>";
                $this->Common->sendEmail($orgval["User"]["email"], $subject, $template, $viewVars, $cc = false, $bcc = false, $attachments = false);
            }
        }
        return true;
    }

    public function pushnotify() {
        $deviceToken_msg_arr = array();
        $token = "f5f670a959e2e7cddba902e59884e2ce5cf4f197e3c54c385047d126d1586b2c";
        $count = 1;
        $parameter = array("msg" => "Good Beverages", "core_values" => array("1", "2"), "org_id" => 29, "category" => "SwitchAction", "notification_type" => "post_promotion",
            "title" => "nDorse App");


        //$msg = 'Hi '.$endorsement['EndorsedUser']['fname']." , you have endorsed by ".$endorsement['Endorser']['fname'];
        $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => 'Hi, you have endoresed', 'data' => $parameter);
        //print_r($deviceToken_msg_arr);
        $this->Common->sendPushNotification($deviceToken_msg_arr);
        exit;
    }

    public function topendorse($type = "week") {

        $monthname = "";
        if ($type == "month") {
            $this->Topendorser->deleteAll(
                    array('Topendorser.type' => "month")
            );
            echo $start_date = date('Y-m-d 00:00:00', strtotime("first day of -1 month"));
            //echo $start_date = date('Y-m-d',strtotime('-2 Monday')); //last Monday
            //exit;
            $monthname = date('F', strtotime($start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime('last day of -1 month'));
        } else {
            $this->Topendorser->deleteAll(
                    array('Topendorser.type' => "week")
            );
            $start_date = date('Y-m-d 00:00:00', strtotime('-2 week monday 00:00:00'));
            //echo $start_date = date('Y-m-d',strtotime('-2 Monday')); //last Monday
            //exit;
            $end_date = date('Y-m-d 23:59:59', strtotime('-1 week sunday 23:59:59'));
        }


        $params = array();
        $params['fields'] = "count(Endorsement.endorser_id) as cnt,Endorsement.endorser_id,User.id, User.fname ,User.lname,User.email,Endorsement.organization_id,Org.name,LoginStatistics.*";
        //$conditionarray["Endorsement.endorsement_for"] = 'user';
        $conditionarray["Endorsement.created >= "] = $start_date;
        $conditionarray["Endorsement.created <= "] = $end_date;
        $params['joins'] = array(
            array(
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => array(
                    'Endorsement.endorser_id =User.id '
                )
            ), array(
                'table' => 'user_organizations',
                'alias' => 'UserOrganization',
                'type' => 'INNER',
                'conditions' => array(
                    'Endorsement.endorser_id =UserOrganization.user_id ',
                    'UserOrganization.status =1'
                )
            )
            ,
            array(
                'table' => 'login_statistics',
                'alias' => 'LoginStatistics',
                'type' => 'LEFT',
                'conditions' => array(
                    'LoginStatistics.user_id =User.id',
                    'LoginStatistics.live =1'
                )
            ),
            array(
                'table' => 'organizations',
                'alias' => 'Org',
                'type' => 'INNER',
                'conditions' => array(
                    'Org.id =Endorsement.organization_id '
                )
            )
        );
        $params['group'] = 'Endorsement.endorser_id,Endorsement.organization_id';
        $params['order'] = array('cnt DESC');
        $params['conditions'] = $conditionarray;
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
        $topendorserd = $this->Endorsement->find("all", $params);
        //	echo $this->Endorsement->getLastQuery();exit;
        $endorserarray = array();
        //print_r($topendorserd);
        $alluser = array();
        $topendorseduser = array();
        $endorseruser = array();
        $organization_name = array();
        foreach ($topendorserd as $val) {
            //	print_r($val[0]["cnt"]);
            if (!isset($endorserarray[$val["Endorsement"]["organization_id"]])) {
                $endorserarray[$val["Endorsement"]["organization_id"]] = $val[0]["cnt"];
                $topendorseduser[$val["Endorsement"]["organization_id"]] = $val["User"]["id"];

                if (!in_array($val["Endorsement"]["organization_id"], $organization_name)) {
                    $organization_name[$val["Endorsement"]["organization_id"]] = $val["Org"]["name"];
                }
                if (!in_array($val["User"]["id"], $alluser)) {
                    $alluser[$val["User"]["id"]] = array_merge($val["User"], $val["LoginStatistics"]);
                }
            } else {
                if ($val[0]["cnt"] > $endorserarray[$val["Endorsement"]["organization_id"]]) {
                    $endorserarray[$val["Endorsement"]["organization_id"]] = $val[0]["cnt"];
                    $topendorseduser[$val["Endorsement"]["organization_id"]] = $val["User"]["id"];
                    if (!in_array($val["User"]["id"], $alluser)) {
                        $alluser[$val["User"]["id"]] = array_merge($val["User"], $val["LoginStatistics"]);
                    }
                } elseif ($val[0]["cnt"] == $endorserarray[$val["Endorsement"]["organization_id"]]) {
                    $previous_arr = $topendorseduser[$val["Endorsement"]["organization_id"]];
                    if (!in_array($val["User"]["id"], $alluser)) {
                        $alluser[$val["User"]["id"]] = array_merge($val["User"], $val["LoginStatistics"]);
                    }
                    $topendorseduser[$val["Endorsement"]["organization_id"]] .= "," . $val["User"]["id"];
                }
            }
        }

//              print_r($endorserarray);
//			   echo "<hr>";
//			  print_r($alluser);
//			  echo "<hr>";
//			  print_r($organization_name);
//			  echo "<hr>";
//			  print_r($topendorseduser);
        $deviceToken_ios_msg_arr = array();
        foreach ($topendorseduser as $key => $val) {
            echo "<hr>";
            echo $key . "===" . $val;
            echo "<hr>";
            echo "organization: " . $organization_name[$key];
            echo "<hr>";
            echo $val;
            $topuser = explode(",", $val);

            $topendorserorg = $this->Topendorser->find("first", array("conditions" => array("organization_id" => $key, "type" => $type)));
            print_r($topendorserorg);

            if (!empty($topendorserorg)) {
                $topendorsed[] = array("organization_id" => $key, "endorser" => $val, "type" => $type, "id" => $topendorserorg["Topendorser"]["id"]);
            } else {
                $topendorsed[] = array("organization_id" => $key, "endorser" => $val, "type" => $type);
            }
            foreach ($topuser as $uval) {
                print_r($alluser[$uval]);
                $endorseusr = $alluser[$uval];
                if (isset($endorseusr["LoginStatistics"]) && strtolower($endorseusr["LoginStatistics"]["os"]) == "ios") {
                    //print_r($device_token);
                    //$deviceToken_msg_arr = array();
                    if ($endorseusr["LoginStatistics"]["device_id"] != "") {
                        $token = $endorsedUser["LoginStatistics"]["device_id"];
                        $count = 1;
                        $msg = 'Hi ' . $endorseusr["fname"] . " , you have Top nDorsed in this " . $type . " on organization : " . $organization_name[$key];
                        //$data = array("org_id"=>$key);
                        //$deviceToken_ios_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg);
                        $parameter = array("org_id" => $key, "category" => "SwitchAction", "notification_type" => "post_promotion",
                            "title" => "nDorse App");


                        $deviceToken_ios_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
                    }
                    //print_r($deviceToken_msg_arr);
                    //  $this->Common->sendPushNotification($deviceToken_msg_arr);
                } elseif (isset($endorsedUser["LoginStatistics"]) && strtolower($endorsedUser["LoginStatistics"]["os"]) == "android") {
                    
                }

                //$viewVars = array("org_name" => $organization_name[$key],"fname"=>$endorseusr["fname"],"type"=>"week");
                $viewVars = array("org_name" => $organization_name[$key], "endorse_name" => "nDorsed", "fname" => $endorseusr["fname"], "type" => $type);
                //echo $subject = "Ndorse notification :Top endorser for this ".$organization_name[$key];
                if ($type == "month") {
                    $subject = "Top nDorsed Of The $type -" . $monthname . " Organization :" . $organization_name[$key];
                } else {
                    $subject = "Top nDorsed Of The " . $type . " Organization :" . $organization_name[$key];
                }
                echo $endorseusr["email"];
                //$endorseusr["email"] ="msharma@arcgate.com";
                //$this->Common->sendEmail($endorseusr["email"], $subject, "Top_endorser", $viewVars, $cc = false, $bcc = false, $attachments = false);
                if ($type == "week") {
                    $subject = "nDorse Notification :Top nDorsed Of The " . $type . " Organization :" . $organization_name[$key];
                    $this->notifyendorse($key, $viewVars, $uval, $subject, "nDorser");
                }
            }
        }
        //  $this->Topendorser->saveMany($topendorsed);

        if (!empty($deviceToken_ios_msg_arr)) {
            $this->Common->sendPushNotification($deviceToken_ios_msg_arr);
        }
        $this->topendorsed($type);
        exit;
    }

    public function topendorsed($type = "week") {
        $monthname = "";

        if ($type == "month") {
            echo $start_date = date('Y-m-d 00:00:00', strtotime("first day of -1 month"));
            //echo $start_date = date('Y-m-d',strtotime('-2 Monday')); //last Monday
            //exit;
            $monthname = date('F', strtotime($start_date));
            $end_date = date('Y-m-d 23:59:59', strtotime('last day of -1 month'));
        } else {
            $start_date = date('Y-m-d 00:00:00', strtotime('--2 week monday 00:00:00'));
            //echo $start_date = date('Y-m-d',strtotime('-2 Monday')); //last Monday
            //exit;
            $end_date = date('Y-m-d 23:59:59', strtotime('-1 week sunday 23:59:59'));
        }


        $params = array();
        $params['fields'] = "count(Endorsement.endorsed_id) as cnt,Endorsement.endorsed_id,User.id, User.fname ,User.lname,User.email,Endorsement.organization_id,Org.name,LoginStatistics.*";
        $conditionarray["Endorsement.endorsement_for"] = 'user';
        $conditionarray["Endorsement.created >= "] = $start_date;
        $conditionarray["Endorsement.created <= "] = $end_date;
        $params['joins'] = array(
            array(
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => array(
                    'Endorsement.endorsed_id =User.id '
                )
            ),
            array(
                'table' => 'user_organizations',
                'alias' => 'UserOrganization',
                'type' => 'INNER',
                'conditions' => array(
                    'Endorsement.endorsed_id =UserOrganization.user_id ',
                    'UserOrganization.status =1'
                )
            ),
            array(
                'table' => 'login_statistics',
                'alias' => 'LoginStatistics',
                'type' => 'LEFT',
                'conditions' => array(
                    'LoginStatistics.user_id =User.id',
                    'LoginStatistics.live =1'
                )
            ),
            array(
                'table' => 'organizations',
                'alias' => 'Org',
                'type' => 'INNER',
                'conditions' => array(
                    'Org.id =Endorsement.organization_id '
                )
            )
        );
        $params['group'] = 'Endorsement.endorsed_id,Endorsement.organization_id';
        $params['order'] = array('cnt DESC');
        $params['conditions'] = $conditionarray;
        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
        $topendorserd = $this->Endorsement->find("all", $params);
        //echo $this->Endorsement->getLastQuery();exit;
        $endorserarray = array();
        //print_r($topendorserd);
        $alluser = array();
        $topendorseduser = array();
        $endorseruser = array();
        $organization_name = array();
        foreach ($topendorserd as $val) {
            //	print_r($val[0]["cnt"]);
            if (!isset($endorserarray[$val["Endorsement"]["organization_id"]])) {
                $endorserarray[$val["Endorsement"]["organization_id"]] = $val[0]["cnt"];
                $topendorseduser[$val["Endorsement"]["organization_id"]] = $val["User"]["id"];

                if (!in_array($val["Endorsement"]["organization_id"], $organization_name)) {
                    $organization_name[$val["Endorsement"]["organization_id"]] = $val["Org"]["name"];
                }
                if (!in_array($val["User"]["id"], $alluser)) {
                    $alluser[$val["User"]["id"]] = array_merge($val["User"], $val["LoginStatistics"]);
                }
            } else {
                if ($val[0]["cnt"] > $endorserarray[$val["Endorsement"]["organization_id"]]) {
                    $endorserarray[$val["Endorsement"]["organization_id"]] = $val[0]["cnt"];
                    $topendorseduser[$val["Endorsement"]["organization_id"]] = $val["User"]["id"];
                    if (!in_array($val["User"]["id"], $alluser)) {
                        $alluser[$val["User"]["id"]] = array_merge($val["User"], $val["LoginStatistics"]);
                    }
                } elseif ($val[0]["cnt"] == $endorserarray[$val["Endorsement"]["organization_id"]]) {
                    $previous_arr = $topendorseduser[$val["Endorsement"]["organization_id"]];
                    if (!in_array($val["User"]["id"], $alluser)) {
                        $alluser[$val["User"]["id"]] = array_merge($val["User"], $val["LoginStatistics"]);
                    }
                    $topendorseduser[$val["Endorsement"]["organization_id"]] .= "," . $val["User"]["id"];
                }
            }
        }

//              print_r($endorserarray);
//			   echo "<hr>";
//			  print_r($alluser);
//			  echo "<hr>";
//			  print_r($organization_name);
//			  echo "<hr>";
//			  print_r($topendorseduser);

        foreach ($topendorseduser as $key => $val) {
            //echo "<hr>";
            //echo $key."===".$val;
            //echo "<hr>";
            echo $type . " " . $key;
            $topendorserorg = $this->Topendorser->find("first", array("conditions" => array("organization_id" => $key, "type" => $type)));
            print_r($topendorserorg);

            // $topendorsed["Topendorser"][] = array("organization_id" => $key, "endorsed" => $val,"type"=>$type);
            if (!empty($topendorserorg)) {
                $topendorsed[] = array("organization_id" => $key, "endorsed" => $val, "type" => $type, "id" => $topendorserorg["Topendorser"]["id"]);
            } else {
                $topendorsed[] = array("organization_id" => $key, "endorsed" => $val, "type" => $type);
            }
            echo "organization: " . $organization_name[$key];
            echo "<hr>";
            $topuser = explode(",", $val);
            $deviceToken_ios_msg_arr = array();
            foreach ($topuser as $uval) {
                //print_r($alluser[$uval]);
                $endorseusr = $alluser[$uval];
                if (isset($endorseusr["LoginStatistics"]) && strtolower($endorseusr["LoginStatistics"]["os"]) == "ios") {
                    //print_r($device_token);
                    //$deviceToken_msg_arr = array();
                    if ($endorseusr["LoginStatistics"]["device_id"] != "") {
                        $token = $endorsedUser["LoginStatistics"]["device_id"];
                        $count = 1;
                        $msg = 'Hi ' . $endorseusr["fname"] . " , you have Top nDorser in this " . $type . " on organization : " . $organization_name[$key];
                        $data = array("org_id" => $key);
                        $deviceToken_ios_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg);
                    }
                    //print_r($deviceToken_msg_arr);
                    //  $this->Common->sendPushNotification($deviceToken_msg_arr);
                } elseif (isset($endorsedUser["LoginStatistics"]) && strtolower($endorsedUser["LoginStatistics"]["os"]) == "android") {
                    
                }
                $viewVars = array("org_name" => $organization_name[$key], "endorse_name" => "nDorser", "fname" => $endorseusr["fname"], "type" => $type);
                //Top nDorser Of The Month - <MonthName>
                if ($type == "month") {
                    $subject = "Top nDorser Of The $type -" . $monthname . " Organization :" . $organization_name[$key];
                } else {
                    $subject = "Top nDorser Of The " . $type . " Organization :" . $organization_name[$key];
                }
                echo $endorseusr["email"];
                //$endorseusr["email"] ="msharma@arcgate.com";
                //$this->Common->sendEmail($endorseusr["email"], $subject, "Top_endorser", $viewVars, $cc = false, $bcc = false, $attachments = false);
                if ($type == "week") {
                    $subject = "nDorse Notification :Top nDorser Of The " . $type . " Organization :" . $organization_name[$key];
                    $this->notifyendorse($key, $viewVars, $uval, $subject, "nDorsed");
                }
            }
        }
        print_r($topendorsed);
        // $this->Topendorser->saveMany($topendorsed);

        if (!empty($deviceToken_ios_msg_arr)) {
            $this->Common->sendPushNotification($deviceToken_ios_msg_arr);
        }


        exit;
    }

    public function orghealth() {


//		select count(user_organizations.user_id) as cnt,user_organizations.organization_id from user_organizations
//where user_organizations.status  IN (0, 1, 3)
//
//group by  user_organizations.organization_id

        $array = array();
        $array['fields'] = array('count(UserOrganization.user_id) as cnt', 'UserOrganization.organization_id');
        $conditionarray = array();
        $conditionarray['UserOrganization.status'] = 1; // array('0','1','3');
        $array['joins'] = array(
            array(
                'table' => 'organizations',
                'alias' => 'Organization',
                'type' => 'INNER',
                'conditions' => array(
                    'Organization.id =UserOrganization.organization_id ',
                    'Organization.status =1'
                )
            )
        );
        $array['conditions'] = $conditionarray;
        $array['order'] = 'cnt desc';
        $array['group'] = 'UserOrganization.organization_id';
        $this->UserOrganization->unbindModel(array('belongsTo' => array('Organization', 'User')));
        $orgArray = $this->UserOrganization->find("all", $array);
        // echo $this->UserOrganization->getLastQuery();die;
        //print_r($orgArray);
        $organization = array();
        foreach ($orgArray as $val) {
            //print_r($val);

            $organization[$val["UserOrganization"]["organization_id"]] = $val[0]["cnt"];
        }
        //print_r($organization);

        $start_date = date('Y-m-d 00:00:00', strtotime('-8 days'));
        //echo $start_date = date('Y-m-d',strtotime('-2 Monday')); //last Monday
        //exit;
        $end_date = date('Y-m-d 23:59:59', strtotime('-1 days'));



        $conditionarray = array();
        $conditionarray["Endorsement.created >= "] = $start_date;
        $conditionarray["Endorsement.created <= "] = $end_date;
        $total_week_days = 7;
        foreach ($organization as $key => $val) {
            //echo $key."---".$val;

            echo "<hr>----<hr>";
            $array = array();
            echo $org_id = $key;
            echo "<hr>";
            $total_user = $val;

            $array['fields'] = array('count(Endorsement.id) as cnt', 'DATE_FORMAT(Endorsement.created,"%Y-%m-%d") as cdate');

            $conditionarray['Endorsement.organization_id'] = $org_id; // array('0','1','3');

            $array['conditions'] = $conditionarray;
            $array['order'] = 'Endorsement.created desc';
            $array['group'] = 'DATE_FORMAT(Endorsement.created, "%Y-%m-%d")';

            $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
            $totalendorsement = $this->Endorsement->find("all", $array);
            //print_r($totalendorsement);
            $totaldaysendorsed = count($totalendorsement);
            $totalendorse = 0;
            $endorseuser = 0;
            if ($totaldaysendorsed > 0) {
                foreach ($totalendorsement as $totcount) {
                    print_r($totcount);
                    $totalendorse +=$totcount[0]["cnt"];
                }
                //
                $array['fields'] = array('Endorsement.id', 'Endorsement.endorser_id');
                $array['group'] = 'Endorsement.endorser_id';
                $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                $totaluserendorsement = $this->Endorsement->find("all", $array);
                //print_r($totaluserendorsement);
                $endorseuser = count($totaluserendorsement);
            }
            $health_array = array("org_id" => $org_id, "total_day" => $total_week_days, "total_user" => $total_user, "total_endorse" => $totalendorse, "total_endorsed_user" => $endorseuser, "total_endorsed_day" => $totaldaysendorsed);
            echo $health_value = $this->total_health($health_array);

            if ($health_value >= 95) {
                $positive_health_url = "most_happy.png";
            } elseif ($health_value >= 80) {
                $positive_health_url = "happy.png";
            } elseif ($health_value >= 60) {
                $positive_health_url = "normal_above.png";
            } elseif ($health_value >= 40) {
                $positive_health_url = "normal.png";
            } else {
                $positive_health_url = "sad.png";
            }
            echo $positive_health_url;
            $this->Organization->id = $org_id;
            $this->Organization->savefield("health_score", $health_value);
            $this->Organization->savefield("health_url", $positive_health_url);
            //echo "Total user:".$total_user;
            //echo "<hr>";
            //echo "Total days endorsed:".$totaldaysendorsed;
            //echo "<hr>";
            //echo "Total endorse:".$totalendorse;
            //echo "<hr>";
            //echo "Total endorsed user:".$endorseuser;
        }
        exit;


//
//
//select count(`endorsements`.`id`) as cnt,DATE_FORMAT(created,"%Y-%m-%d") as cdate,endorsements.organization_id from endorsements
//where
//endorsements.organization_id=29  AND
//endorsements.`created`>= '2016-05-11 00:00:00' AND
//endorsements.`created` <= '2016-05-1923:59:59'
//group by  DATE_FORMAT(created, "%Y-%m-%d") , endorsements.organization_id order by created desc
//
//select * from endorsements where organization_id=292 and 
//endorsements.created>= '2016-05-11 00:00:00' AND
//endorsements.created <= '2016-05-1923:59:59'
// # endorsements.created>= '2016-05-19 00:00:00' 
//group by endorser_id
// order by created desc
        exit;
    }

    public function topendorsemonth() {
        echo "endorsemonth";
        exit;
    }

    public function weekelyEndorseNotification() {
        $dateBefore7Days = date('Y-m-d', strtotime('-7 days'));

        $params = array();
        $params['fields'] = array("*");
        $params['conditions'] = array();
        $params['joins'] = array(
            array(
                'table' => 'users',
                'alias' => 'Endorser',
                'type' => 'LEFT',
                'conditions' => array(
                    'Endorsement.endorser_id = Endorser.id '
                )
            )
        );
        $params['group'] = "Endorsement.endorser_id, Endorsement.organization_id";

        $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));

        $this->Endorsement->find("all", $params);
        echo $this->Endorsement->getLastQuery();
        die;
    }

    public function total_health($data) {
        print_r($data);
        $total_endorsed_endorseby_health = 0;
        $total_endorsed_userby_health = 0;
        $total_health = 0;
        if ($data["total_endorsed_day"] > 0) {
            echo $total_endorsed_endorseby_health = ($data["total_endorsed_day"] / $data["total_day"]) * ($data["total_endorse"] / $data["total_user"]);
            echo "<hr>";

            if ($data["total_endorsed_user"] > $data["total_user"]) {
                $user_cal = 1;
            } else {
                $user_cal = $data["total_endorsed_user"] / $data["total_user"];
            }
            echo $total_endorsed_userby_health = ($data["total_endorsed_day"] / $data["total_day"]) * ($user_cal);
            echo "<hr>";
            echo $total_health = (($total_endorsed_endorseby_health + $total_endorsed_userby_health) / 2) * 100;
            echo "<hr>";
            echo "<hr>";
        }
        return $total_health;
    }

//select User.id,User.fname,User.lname,User.email,`Organization`.name,`Endorsement`.`endorser_id`,`LoginStatistics`.* from `user_organizations`
//inner join users as User on (`user_organizations`.user_id = User.id)
//inner join organizations as Organization on (`user_organizations`.organization_id = Organization.id and Organization.status=1)
//left join login_statistics as LoginStatistics ON (`LoginStatistics`.`user_id` = `user_organizations`.`user_id` and `LoginStatistics`.`live` >= 1) 
//left join endorsements as Endorsement ON (`Endorsement`.`endorser_id` = `user_organizations`.`id` and `Endorsement`.`created` >= '2016-05-16' ) 
//where  `user_organizations`.status =1
//GROUP BY `user_organizations`.`user_id`, `user_organizations`.`organization_id`

    public function weekelyEndorseAlert() {
        $current_date = time();
        $params = array();
        $params['fields'] = array("User.id,User.fname,User.lname,User.email,User.notification_unsubscribed,`Organization`.name,`Organization`.id,`Endorsement`.`endorser_id`,UserOrganization.created as usercreated,`Endorsement`.`created`,`LoginStatistics`.* ");
        // $params['conditions'] = array();
        $params['joins'] = array(
            array(
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => array(
                    'UserOrganization.user_id = User.id',
                    'UserOrganization.status =1'
                )
            ),
            array(
                'table' => 'organizations',
                'alias' => 'Organization',
                'type' => 'INNER',
                'conditions' => array(
                    'UserOrganization.organization_id = Organization.id ',
                    'Organization.status =1'
                )
            )
            ,
            array(
                'table' => 'login_statistics',
                'alias' => 'LoginStatistics',
                'type' => 'LEFT',
                'conditions' => array(
                    'LoginStatistics.user_id = UserOrganization.user_id ',
                    'LoginStatistics.live =1'
                )
            ), array(
                'table' => 'endorsements',
                'alias' => 'Endorsement',
                'type' => 'LEFT',
                'conditions' => array(
                    'Endorsement.endorser_id= UserOrganization.user_id ',
                    'Endorsement.organization_id =UserOrganization.organization_id'
                )
            )
        );
        $params['group'] = "UserOrganization.user_id, UserOrganization.organization_id";
        $params['order'] = "Endorsement.created desc";

        // $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
        $this->UserOrganization->unbindModel(array('belongsTo' => array('User', 'Organization')));
        $userdata = $this->UserOrganization->find("all", $params);
        //print_r($userdata);
        // echo $this->UserOrganization->getLastQuery();die;
        $noendorsementuser = array();
        $deviceToken_ios_msg_arr = array();
        //print_r($userdata);exit;
        foreach ($userdata as $data) {

            //email escape for "lucus.shelton@lumc.edu"added by Babulal Prasad @ 12222016
            //if(isset($data['User']['email']) && $data['User']['email'] !='lucus.shelton@lumc.edu'){
            if (isset($data['User']['email']) && $data['User']['email'] != 'lucus.shelton@lumc.edu' && $data['User']['notification_unsubscribed'] == 0) {

                $emailflag = 0;

                // echo "<hr>";
                // echo  $data["User"]["email"];
                if ($data["Endorsement"]["endorser_id"] == "") {


                    $difftime = $current_date - strtotime($data["UserOrganization"]["usercreated"]);

                    $daydiff = round($difftime / 86400);
                    /** Days increase from 7 to 21 by Babulal prasad as per client requirement @22-feb-2017 * */
//                    if ($daydiff % 7 == 0 && $daydiff > 0) {
//                        $emailflag = 1;
//                        $weekdays = $daydiff / 7;
//                    }
                    if ($daydiff % 21 == 0 && $daydiff > 0) {
                        $emailflag = 1;
                        $weekdays = $daydiff / 21;
                    }
                    //echo $emailflag;
                } else {
                    //   $params['conditions'] = $conditionarray;
                    $params = array();
                    $params['order'] = 'Endorsement.created desc';
                    $conditionarray["Endorsement.organization_id"] = $data["Organization"]["id"];
                    $conditionarray["Endorsement.endorser_id"] = $data["User"]["id"];
                    $params['conditions'] = $conditionarray;
                    $this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
                    $allendorsement = $this->Endorsement->find("first", $params);

                    $difftime = $current_date - strtotime($allendorsement["Endorsement"]["created"]);
                    //echo "<hr>";
                    //echo $allendorsement["Endorsement"]["created"];
                    // echo "<hr>";
                    //echo strtotime($allendorsement["Endorsement"]["created"])."---".$current_date;
                    // echo "<hr>";
                    $daydiff = round($difftime / 86400);
                    /** Days increase from 7 to 21 by Babulal prasad as per client requirement @22-feb-2017 * */
//                    if ($daydiff % 7 == 0 && $daydiff > 0) {
//                        $emailflag = 1;
//                        $weekdays = $daydiff / 7;
//                    }
                    if ($daydiff % 21 == 0 && $daydiff > 0) {
                        $emailflag = 1;
                        $weekdays = $daydiff / 21;
                    }
                }
                //echo "<hr>";
                // echo $emailflag;
                //  echo "<hr>";
                if ($emailflag == 1) {
                    //echo $data["User"]["email"];
                    //    if ($emailflag == 1 ) {

                    if (isset($data["LoginStatistics"]) && strtolower($data["LoginStatistics"]["os"]) == "ios" && strtolower($data["LoginStatistics"]["device_id"]) != "") {
                        echo $data["User"]["email"];
                        echo "<hr>";
                        echo "ios";
                        echo "<hr>";
                        //print_r($device_token);
                        //$deviceToken_msg_arr = array();
                        $token = $data["LoginStatistics"]["device_id"];
                        $count = 1;
                        //we notice that you are not ndorsing your colleagues in your nDorse organization
                        // You have not endorsed anybody in last 1 week in nDorse organization Architect.
                        //Hi <username>, we notice that you are not ndorsing your colleagues in your nDorse organization:<orgname> from more than 7 days.
                        // $msg = 'Hey ' . $data["User"]["fname"] . " , we notice that you are not ndorsing your colleagues in your nDorse organization: " . $data["Organization"]["name"]." from more than $weekdays week";
                        $msg = "Hey " . $data["User"]["fname"] . ", we noticed that you have not nDorsed a colleage/friend in " . $data["Organization"]["name"] . " from some time -  nDorse someone now! Motivate with Praise!!";
                        $parameter = array("org_id" => $data["Organization"]["id"], "category" => "SwitchAction", "notification_type" => "post_promotion",
                            "title" => "nDorse App");


                        $deviceToken_ios_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
                        //print_r($deviceToken_msg_arr);
                        $deviceToken_msg_arr = array();
                        $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
                        // print_r($deviceToken_msg_arr);
                        $this->Common->sendPushNotification($deviceToken_msg_arr);
                        //echo "<hr>";
                        //exit;
                    } elseif (isset($data["LoginStatistics"]) && strtolower($data["LoginStatistics"]["os"]) == "android" && strtolower($data["LoginStatistics"]["device_id"]) != "") {
                        echo $data["User"]["email"];
                        echo "<hr>";
                        echo "android";
                        echo "<hr>";
                        $token = $data["LoginStatistics"]["device_id"];
                        $count = 1;
                        //$msg = 'Hi ' . $data["User"]["fname"] . " , you are not endorse in a nDorse organizatin :" . $data["Organization"]["name"];
                        // $msg = 'Hi ' . $data["User"]["fname"] . " , we notice that you are not ndorsing your colleagues in your nDorse organization: " . $data["Organization"]["name"]." from more than $weekdays week";
                        $msg = "Hey " . $data["User"]["fname"] . ", we noticed that you have not nDorsed a colleage/friend in " . $data["Organization"]["name"] . " from some time -  nDorse someone now! Motivate with Praise!!";
                        $parameter = array("org_id" => $data["Organization"]["id"], "category" => "SwitchAction", "notification_type" => "post_promotion",
                            "title" => "nDorse App");


                        $deviceToken_android_msg_arr = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
                        //print_r($deviceToken_android_msg_arr);
                        // echo "<hr>";
                        $this->Common->sendPushNotificationAndroidcommon($deviceToken_android_msg_arr);
                    }


                    $noendorsementuser[] = $data;
                    $email = $data["User"]["email"];
                    $fname = $viewVars["fname"] = $data["User"]["fname"];
                    $organization_name = $viewVars["organization_name"] = $data["Organization"]["name"];
                    $subject = "nDorse notification : no nDorsments from some time in " . $organization_name;
                    $viewVars["days"] = $weekdays;

                    $userIdEncrypted = base64_encode($data["User"]["id"]);
                    $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                    $viewVars["pathToRender"] = $pathToRender;
                    //$email ="msharma@arcgate.com";
                    $this->Common->sendEmail($email, $subject, "no_endorser_week", $viewVars, $cc = false, $bcc = false, $attachments = false);
                }
            }
        }
        //print_r($deviceToken_ios_msg_arr);
        if (!empty($deviceToken_ios_msg_arr)) {
            // $this->Common->sendPushNotification($deviceToken_ios_msg_arr);
        }
        // print_r($noendorsementuser);

        exit;
    }

    public function subscriptionReminderCron() {
        $params = array();
        $params['fields'] = array("*");
        $params['conditions'] = array();
        $params['joins'] = array(
            array(
                'table' => 'users',
                'alias' => 'Admin',
                'type' => 'LEFT',
                'conditions' => array(
                    'Subscription.user_id = Admin.id'
                )
        ));

        $subscriptions = $this->Subscription->query("SELECT * , DATEDIFF(Subscription.end_date, CURDATE()) as days_left
                                                    FROM `ndorse`.`subscriptions` AS `Subscription` 
                                                    LEFT JOIN `ndorse`.`users` AS `Admin` ON (`Subscription`.`user_id` = `Admin`.`id`) 
                                                    WHERE Subscription.id IN (
                                                        SELECT MAX(id) FROM subscriptions 
                                                        GROUP BY subscriptions.`organization_id`
                                                    )
                                                    AND ( DATEDIFF(Subscription.end_date, CURDATE()) IN (7, 3))");
        pr($subscriptions);
        die;

        foreach ($subscriptions as $subscription) {
            $to = $subscription['Admin']['email'];
            $subject = "nDorse notification -- subscription period about to complete";
            $template = "subscription_complete";
            $configVars = array("first_name" => $subscription['Admin']['fname'], "days_left" => $subscription[0]['days_left']);
            $this->Common->sendEmail($email, $subject, $template, $configVars, $cc = false, $bcc = false, $attachments = false);
        }
    }

    public function notifyendorse($org_id, $data, $notid, $subject, $ndorsetype) {
        $endorserUsers = $this->UserOrganization->find("all", array(
            "joins" => array(
                array('table' => "login_statistics",
                    "alias" => "LoginStatistics",
                    "type" => "LEFT",
                    'conditions' => array(
                        'LoginStatistics.user_id =User.id AND LoginStatistics.live =1'
                    )
                )
            ),
            "conditions" => array("organization_id" => $org_id, "UserOrganization.status" => 1, "UserOrganization.user_id !=" => $notid),
            "fields" => array("User.email", "User.fname", "User.lname", "LoginStatistics.*")));
        //echo $this->UserOrganization->getLastQuery();die;
        $deviceToken_ios_msg_arr = array();
        if (!empty($endorserUsers)) {
            foreach ($endorserUsers as $userval) {
                if (isset($userval["LoginStatistics"]) && strtolower($userval["LoginStatistics"]["os"]) == "ios") {
                    //print_r($device_token);
                    //$deviceToken_msg_arr = array();
                    if ($userval["LoginStatistics"]["device_id"] != "") {
                        $token = $userval["LoginStatistics"]["device_id"];
                        $count = 1;
                        // Hi <user name>, Congraulations! You are the  Top nDorser/nDorsed of this week/month in the organization : <organizaion name>.
                        $msg = 'Hi ' . $userval["User"]["fname"] . ", Congraulations! You are the  Top " . $ndorsetype . " of this " . $data["type"] . " in the Organization : " . $data["org_name"] . ".";
                        // Hi Amit, you are the top endorsed of this week in the organization: ArcGate.
                        //$data = array("org_id"=>$key);
                        //$deviceToken_ios_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg);
                        $parameter = array("org_id" => $org_id, "category" => "SwitchAction", "notification_type" => "post_promotion",
                            "title" => "nDorse App");


                        $deviceToken_ios_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
                    }
                    //print_r($deviceToken_msg_arr);
                    //  $this->Common->sendPushNotification($deviceToken_msg_arr);
                } elseif (isset($endorsedUser["LoginStatistics"]) && strtolower($endorsedUser["LoginStatistics"]["os"]) == "android") {
                    if ($userval["LoginStatistics"]["device_id"] != "") {
                        $token = $userval["LoginStatistics"]["device_id"];
                        $count = 1;

                        $msg = 'Hi ' . $userval["User"]["fname"] . ", You are the   Top " . $ndorsetype . " of this " . $data["type"] . " in the organization : " . $data["org_name"];
                        // Hi Amit, you are the top endorsed of this week in the organization: ArcGate.
                        //$data = array("org_id"=>$key);
                        //$deviceToken_ios_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg);
                        $parameter = array("org_id" => $org_id, "category" => "SwitchAction", "notification_type" => "post_promotion",
                            "title" => "nDorse App");


                        $deviceToken_android_msg_arr = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
                        $this->Common->sendPushNotificationAndroid($deviceToken_android_msg_arr);
                    }
                }
                //print_r($data);
                $data["username"] = $userval["User"]["fname"];
                //$userval["User"]["email"] ="msharma@arcgate.com";
                //$this->Common->sendEmail($userval["User"]["email"], $subject, "Top_endorser_alert", $data, $cc = false, $bcc = false, $attachments = false);
            }
            if (!empty($deviceToken_ios_msg_arr)) {
                $this->Common->sendPushNotification($deviceToken_ios_msg_arr);
            }
        }
        //print_r($endorserUsers);
        //print_r($data);
        //$this->Common->sendEmail($endorseusr["email"], $subject, "Top_endorser", $viewVars, $cc = false, $bcc = false, $attachments = false);
        //exit; 
    }

    public function migrationEmailCron() {
        ini_set('memory_limit', '2G');
        $params = array(
            'fields' => array(),
            'conditions' => array('mail_sent' => 0),
            'order' => array('EmailMigration.created ASC'),
            'limit' => 50
        );

        $emailData = $this->EmailMigration->find('all', $params);



        if (!empty($emailData)) {
            foreach ($emailData as $data) {

                if (filter_var($data['EmailMigration']['to'], FILTER_VALIDATE_EMAIL)) {
                    echo $to = $data['EmailMigration']['to'];
                    $subject = $data['EmailMigration']['subject'];
                    $viewVars = unserialize($data['EmailMigration']['config_vars']);
                    $template = $data['EmailMigration']['template'];
                    $bcc = isset($data['EmailMigration']['bcc']) ? $data['EmailMigration']['bcc'] : array();
                    $cc = isset($data['EmailMigration']['cc']) ? $data['EmailMigration']['cc'] : array();
                    //	echo $template;exit;
                    $mailSent = $this->Common->sendEmail($to, $subject, $template, $viewVars, $cc = false, $bcc = false, $attachments = false);
                } else {
                    echo "Invalid Email : " . $data['EmailMigration']['to'];
                    echo "<hr>";

                    $message = $data['EmailMigration']['id'] . " - to : " . $to . " : Email cron - Invalid email";
                    $this->log($message, "email");

                    $mailSent = true;
                }

                echo "<hr>";

                if ($mailSent) {

                    $this->EmailMigration->updateAll(array('mail_sent' => 1), array('id' => $data['EmailMigration']['id']));
                } else {
                    $message = $data['EmailMigration']['id'] . " - to : " . $to . " : EmailMigration cron - mail not sent";
                    $this->log($message, "email");
                }
            }

            echo "Email sent successfully.";
            exit;
        } else {
            echo "No email is pending in queue.";
            exit;
        }
    }

    public function appUseReminderCron() {
        /** Days increase from 7 to 21 by Babulal prasad as per client requirement @22-feb-2017 * */
        $dayDiff = 21;

        $sql = "SELECT *, DATEDIFF(CURDATE(),DATE(last_app_used)) FROM `ndorse`.`users` AS `User` WHERE MOD(DATEDIFF(CURDATE(),DATE(last_app_used)) , 30) = 0 AND DATEDIFF(CURDATE(),DATE(last_app_used)) != 1";
        $params = array();
        $params['fields'] = array("User.*", "ROUND(DATEDIFF(CURDATE(),DATE(last_app_used)) / " . $dayDiff . ") AS time_diff");
        $params['conditions'] = array("MOD(DATEDIFF(CURDATE(),DATE(last_app_used)) , " . $dayDiff . ")" => 0,
            "DATEDIFF(CURDATE(),DATE(last_app_used)) !=" => 0,
            "last_app_used != " => "0000-00-00 00:00:00",
            'User.role != 1',
            "User.status" => 1,
        );
        $params['joins'] = array(
//            array(
//                'table' => 'user_organizations',
//                'alias' => 'UserOrganization',
//                'type' => 'LEFT',
//                'conditions' => array(
//                    'User.id = UserOrganization.user_id '
//                )
//            ),
            array(
                'table' => 'login_statistics',
                'alias' => 'LoginStatistics',
                'type' => 'LEFT',
                'conditions' => array(
                    'LoginStatistics.user_id =User.id ',
                    'LoginStatistics.live =1'
                )
            )
        );
        $params['group'] = "User.id";
        //
        //$this->Endorsement->unbindModel(array('hasMany' => array('EndorseAttachments', 'EndorseCoreValues', 'EndorseReplies')));
//        $this->User->bindModel(
//        array('hasMany' => array(
//                'UserOrganizations' => array(
//                    'className' => 'UserOrganization'
//                )
//            )
//        )
//    );
//        
//         $this->UserOrganization->bindModel(
//        array('belongsTo' => array(
//                'Organization' => array(
//                    'className' => 'Organization'
//                )
//            )
//        )
//    );

        $users = $this->User->find("all", $params);

//        pr($users);die;


        foreach ($users as $user) {
            echo $user['User']['email'];
            //email escape for "lucus.shelton@lumc.edu"added by Babulal Prasad @ 12222016
            //if ($user['User']['email'] != 'lucus.shelton@lumc.edu') {
            if ($user['User']['email'] != 'lucus.shelton@lumc.edu' && $user['User']['notification_unsubscribed'] == 0) {
//            echo "<br>" . $user['User']['email'] . "<br>";
                $params = array();
//           $conditions = array("UserOrganization.user_id" => $user['User']['id']);
                $conditions = array("UserOrganization.status" => 1, "Organization.status" => 1, "UserOrganization.user_id" => $user['User']['id']);
                $params['conditions'] = $conditions;
                pr($params);
                $this->UserOrganization->unbindModel(array('belongsTo' => array('User')));
                $userOrganizations = $this->UserOrganization->find("all", $params);

                if (!empty($userOrganizations)) {
                    echo $to = $user['User']['email'];
                    //echo $to = 'babulal.arcgate@gmail.com';
                    echo '<hr>';

                    // echo $data["User"]["email"];

                    $subject = "nDorse notification -- We have missed your participation!";

                    if ($user[0]['time_diff'] == 1) {
                        $timeDiffDisplay = $user[0]['time_diff'] . " week";
                    } else {
                        $timeDiffDisplay = $user[0]['time_diff'] . " weeks";
                    }

                    $userIdEncrypted = base64_encode($user['User']['id']);
                    $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;

                    $viewVars = array("fname" => $user['User']['fname'], 'time_diff' => $timeDiffDisplay, 'pathToRender' => $pathToRender);
                    $template = "app_use_reminder";
                    //echo $template;exit;
                    $mailSent = $this->Common->sendEmail($to, $subject, $template, $viewVars, $cc = false, $bcc = false, $attachments = false);
                    //
                    print_r($user);
                    echo "<hr>";
                    $loginuser = $this->LoginStatistics->find("all", array(
                        "conditions" => array("user_id" => $user['User']['id'], "live" => 1)
                    ));
                    print_r($loginuser);
                    echo "<hr>";
                    if (!empty($loginuser[0]["LoginStatistics"]) && $loginuser[0]["LoginStatistics"]["device_id"] != "") {

                        if (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "ios") {
                            //print_r($device_token);
                            $deviceToken_msg_arr = array();
                            $token = $loginuser[0]["LoginStatistics"]["device_id"];
                            $count = 1;


                            $count = 1;
                            $msg = 'Hey ' . $user['User']["fname"] . ", we noticed that you have not logged into nDorse App in some time. Log in to see what's new! Recognize a colleague or friend in real time! Motivate with Praise!!";

                            //$msg = 'Hi ' . $user['User']["fname"] . ", we notice that you are not using the app from more than " .($timeDiffDisplay*7)." days." ;
                            // Hi Amit, you are the top endorsed of this week in the organization: ArcGate.
                            //$data = array("org_id"=>$key);
                            //$deviceToken_ios_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg);
                            $parameter = array("");


                            $deviceToken_ios_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
                            print_r($deviceToken_msg_arr);
                            echo $this->Common->sendPushNotification($deviceToken_msg_arr);
                        } elseif (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "android") {

                            $deviceToken_msg_arr = array();
                            $token = $loginuser[0]["LoginStatistics"]["device_id"];
                            $count = 1;
                            //$msg = 'Hi ' . $userval["User"]["fname"] . ", You have not used nDorse app since " .$timeDiffDisplay;
                            //   $msg = 'Hi ' . $user['User']["fname"] . ", we notice that you are not using the app from more than " .($timeDiffDisplay*7)." days." ;
                            $msg = 'Hey ' . $user['User']["fname"] . ", we noticed that you have not logged into nDorse App in some time. Log in to see what's new! Recognize a colleague or friend in real time! Motivate with Praise!!";
                            // Hi Amit, you are the top endorsed of this week in the organization: ArcGate.
                            //$data = array("org_id"=>$key);
                            //$deviceToken_ios_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg);
                            $parameter = array();


                            $deviceToken_android_msg_arr = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
                            //print_r($deviceToken_msg_arr);
                            echo $this->Common->sendPushNotificationAndroid($deviceToken_android_msg_arr);
                        }
                    }
                }
            }
        }
        if (!empty($deviceToken_ios_msg_arr)) {
            $this->Common->sendPushNotification($deviceToken_ios_msg_arr);
        }
        echo "Done";
        die;
    }

    /*
     * Update subscription status and mark users inactive when subscription is cancellled and end date got expire
     */

    public function updateCancelledSubscription() {
        $params = array();
        $conditions = array();
        $conditions['Subscription.is_deleted'] = 1;
        $conditions['Subscription.status'] = 1;
        $conditions['Subscription.end_date <'] = 'CURDATE()';
        $params['conditions'] = $conditions;
        $params['fields'] = array("*");

        $organizations = array();

        $subscriptions = $this->Subscription->find("all", $params);

        foreach ($subscriptions as $subscription) {
            $organizations[] = $subscription['Subscription']['organization_id'];
        }

        if ($this->Subscription->deleteAll(array("organization_id" => $organizations))) {
            $this->UserOrganization->updateAll(array("status" => 0), array("organization_id" => $organizations, "pool_type" => 'paid'));
        }
        echo $this->Subscription->getLastQuery();
        die;

        echo "Cancelled successfully";
        exit;
    }

    public function pushnotifyandroid() {
        $deviceToken_msg_arr = array();
        $token = "APA91bHPoSTHgl0k7FwPzLhZh99tBHBMZVWSUk90I136a4atsGsf83Kv1R0BffIZRtR6WzhWIOfwl0f0dQJBa1NnfWSw5x5qOMBGXi71QxV9AHusP_tQvseCGU1Q_YLWD8unFUfXoAaN";
        $count = 1;
        $parameter = array("org_id" => 29, "category" => "SwitchAction", "notification_type" => "post_promotion",
            "title" => "nDorse App");

        $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => "endorse", "data" => $parameter);
        $parameter = array("msg" => "Good Beverages", "core_values" => array("1", "2"), "org_id" => 29, "category" => "SwitchAction", "notification_type" => "post_promotion",
            "title" => "nDorse App");


        //$msg = 'Hi '.$endorsement['EndorsedUser']['fname']." , you have endorsed by ".$endorsement['Endorser']['fname'];
        $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => 'Hi, you have endoresed', 'data' => $parameter);
        //print_r($deviceToken_msg_arr);
        $this->Common->sendPushNotificationAndroid($deviceToken_msg_arr);
        exit;
    }

    public function syncBadgeCount() {
        $trophy = $this->Trophy->find("first", array("conditions" => array("base_condition" => "count", "type" => "received")));
        $endorseCount = $trophy['Trophy']['base_value'];

        $newBadges = $this->Endorsement->query("
            SELECT  * ,  TRUNCATE( (IFNULL(endorsed_count,0) +  IFNULL(endorser_count,0)/10)  / 10, 0)  as badge_count  FROM
                                                (SELECT Endorsement.* , COUNT(Endorsement.id) as endorsed_count FROM endorsements AS Endorsement 
                                                GROUP BY  Endorsement.endorsed_id , Endorsement.organization_id) AS EndorseeEndorsement
                                                LEFT JOIN
                                                (SELECT Endorsement.* , COUNT(Endorsement.id) as endorser_count FROM endorsements AS Endorsement 
                                                GROUP BY  Endorsement.endorser_id , Endorsement.organization_id )AS EndorserEndorsement
                                                
                                                ON (EndorseeEndorsement.endorsed_id = EndorserEndorsement.endorser_id AND EndorseeEndorsement.organization_id = EndorserEndorsement.organization_id)
                                                
                                                LEFT JOIN users AS User ON (EndorseeEndorsement.endorsed_id = User.id)
                                                LEFT JOIN organizations AS Organization ON (EndorseeEndorsement.organization_id = Organization.id)
                                                LEFT JOIN badges AS Badge ON (EndorseeEndorsement.endorsed_id = Badge.user_id AND EndorseeEndorsement.organization_id = Badge.organization_id  AND Badge.trophy_id = " . $trophy['Trophy']['id'] . ")
                                                WHERE 
                                                    TRUNCATE((IFNULL(endorsed_count,0)  +  IFNULL(endorser_count,0)/10)  / 10 ,0) > 0
                                            ORDER BY EndorseeEndorsement.endorsed_id ASC
        ");
//        pr($newBadges);die;

        foreach ($newBadges as $newBadge) {
            $badge = array();
//            $badge['count'] = 
            if (empty($newBadge['Badge']['id'])) {
                if ($newBadge[0]['badge_count'] != 0 || $newBadge[0]['badge_count'] != "") {
                    $badge['user_id'] = $newBadge['EndorseeEndorsement']['endorsed_id'];
                    $badge['organization_id'] = $newBadge['EndorseeEndorsement']['organization_id'];
                    $badge['trophy_id'] = $trophy['Trophy']['id'];
                    $badge['count'] = $newBadge[0]['badge_count'];
                } else {
                    continue;
                }
            } else {
                $badge = $newBadge['Badge'];
                $badge['count'] = $newBadge[0]['badge_count'];
            }

            $configVars['first_name'] = $newBadge['User']['fname'];
            $configVars['org_name'] = $newBadge['Organization']['name'];

            $saveBadges[] = $badge;
        }


        if (!empty($saveBadges)) {
            $this->Badge->saveMany($saveBadges);
            echo "Badges syncing done";
        } else {
            echo "No badges in queue";
        }
        exit;
    }

    // cron for trial subscription expire
    public function subscriptionexpire() {

        $params = array();
        $conditions = array();
        $conditions['Subscription.payment_method'] = 'ndorse';
        $conditions['Subscription.status'] = 1;
        // $conditions['Subscription.type'] = "trial";
        // $conditions['Subscription.end_date <'] = 'CURDATE()';
        $params['conditions'] = $conditions;
        $params['fields'] = array("*");

        $organizations = array();

        $subscriptions = $this->Subscription->find("all", $params);
        $today = mktime(0, 0, 0, date('m'), date('d'), date('y'));
        //$aftersevenday = mktime(0, 0, 0, date('m'), date('d')+7, date('y')); 
        foreach ($subscriptions as $subscription) {
            $userorgdata = $this->UserOrganization->find("all", array("conditions" => array("organization_id" => $subscription["Organization"]["id"], "user_role" => 2, 'UserOrganization.status' => 1)));
            $emailQueue = array();
            echo $today;
            echo "<hr>";
            $end_date = $subscription["Subscription"]["end_date"];
            $subscriptionyear = date('Y', strtotime($end_date));
            $subscriptionmonth = date('m', strtotime($end_date));
            $subscriptionday = date('d', strtotime($end_date));
            $subscriptionendday = mktime(0, 0, 0, $subscriptionmonth, $subscriptionday, $subscriptionyear);

            $difftime = $subscriptionendday - $today;
            echo $daydiff = round($difftime / 86400);
            echo "<hr>";
            echo $subscription['Organization']['name'];
            echo "<hr>";
            if ($daydiff == 7) {
                echo $subscription["Subscription"]["type"];
                echo "<hr>";
                if ($subscription["Subscription"]["type"] == "trial") {

                    $subject = "nDorse Notification – Trial Subscription Ending!!";
                    foreach ($userorgdata as $orgval) {
                        //$adminorg[] = $uservalorg;
                        $viewVars = array("org_name" => $orgval['Organization']['name'], "fname" => trim($orgval['User']['fname']));
                        /* added by Babulal Prasad at 7-feb-2018 for unsubscribe from email */
                        $userIdEncrypted = base64_encode($orgval['User']['id']);
                        $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                        $viewVars["pathToRender"] = $pathToRender;
                        /**/
                        $configVars = serialize($viewVars);
                        $emailQueue[] = array("to" => $orgval['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "trial_subscription_reminder");
                    }
                } else {
                    $subject = "nDorse notification – Subscription ending! Renew now!!";
                    foreach ($userorgdata as $orgval) {
                        //$adminorg[] = $uservalorg;
                        $viewVars = array("org_name" => $orgval['Organization']['name'], "fname" => trim($orgval['User']['fname']));

                        /* added by Babulal Prasad at 7-feb-2018 for unsubscribe from email */
                        $userIdEncrypted = base64_encode($orgval['User']['id']);
                        $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                        $viewVars["pathToRender"] = $pathToRender;
                        /**/

                        $configVars = serialize($viewVars);
                        $emailQueue[] = array("to" => $orgval['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "paid_subscription_reminder");
                    }
                }
                if (!empty($emailQueue)) {
                    $this->loadModel('Email');
                    $this->Email->saveMany($emailQueue);
                }
            } elseif ($daydiff == 0) {
                if ($subscription["Subscription"]["type"] == "trial") {
                    $subject = "nDorse Notification – Your nDorse App Trial Subscription has expired!";
                    foreach ($userorgdata as $orgval) {
                        //$adminorg[] = $uservalorg;
                        $viewVars = array("org_name" => $orgval['Organization']['name'], "fname" => trim($orgval['User']['fname']));

                        /* added by Babulal Prasad at 7-feb-2018 for unsubscribe from email */
                        $userIdEncrypted = base64_encode($orgval['User']['id']);
                        $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                        $viewVars["pathToRender"] = $pathToRender;
                        /**/

                        $configVars = serialize($viewVars);
                        $emailQueue[] = array("to" => $orgval['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "trial_subscription_remove");
                    }
                    $sqlupdate = "UPDATE user_organizations set status =0  where organization_id='" . $subscription["Organization"]["id"] . "' and status IN (1,3) and pool_type='paid' ";
                    $this->Subscription->query($sqlupdate);
                    $this->Subscription->delete($subscription["Subscription"]["id"]);
                } else {
                    $subject = "nDorse Notification – Subscription ended! Renew to avoid termination!! ";
                    foreach ($userorgdata as $orgval) {
                        //$adminorg[] = $uservalorg;
                        $viewVars = array("org_name" => $orgval['Organization']['name'], "fname" => trim($orgval['User']['fname']));
                        /* added by Babulal Prasad at 7-feb-2018 for unsubscribe from email */
                        $userIdEncrypted = base64_encode($orgval['User']['id']);
                        $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                        $viewVars["pathToRender"] = $pathToRender;
                        /**/
                        $configVars = serialize($viewVars);
                        $emailQueue[] = array("to" => $orgval['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "paid_subscription_reminder2");
                    }
                }
                if (!empty($emailQueue)) {
                    $this->loadModel('Email');
                    $this->Email->saveMany($emailQueue);
                }
            } elseif ($daydiff == -7) {
                $subject = "nDorse Notification – Your nDorse App Subscription has expired! ";
                foreach ($userorgdata as $orgval) {
                    //$adminorg[] = $uservalorg;
                    $viewVars = array("org_name" => $orgval['Organization']['name'], "fname" => trim($orgval['User']['fname']));

                    /* added by Babulal Prasad at 7-feb-2018 for unsubscribe from email */
                    $userIdEncrypted = base64_encode($orgval['User']['id']);
                    $pathToRender = Router::url('/', true) . "unsubscribe/" . $userIdEncrypted;
                    $viewVars["pathToRender"] = $pathToRender;
                    /**/

                    $configVars = serialize($viewVars);
                    $emailQueue[] = array("to" => $orgval['User']['email'], "subject" => $subject, "config_vars" => $configVars, "template" => "paid_subscription_remove");
                }
                if (!empty($emailQueue)) {
                    $this->loadModel('Email');
                    $this->Email->saveMany($emailQueue);
                }
                $sqlupdate = "UPDATE user_organizations set status =0  where organization_id='" . $subscription["Organization"]["id"] . "' and status IN (1,3) and pool_type='paid' ";
                $this->Subscription->query($sqlupdate);
                $this->Subscription->delete($subscription["Organization"]["id"]);
            }
            //echo "<hr>";
            //echo $subscription["Subscription"]["end_date"]."-----".$subscription["Subscription"]["type"];
            //echo "<hr>";
            //$organizations[] = $subscription['Subscription']['organization_id'];
        }
        //
        //if ($this->Subscription->deleteAll(array("organization_id" => $organizations))) {
        //    $this->UserOrganization->updateAll(array("status" => 0), array("organization_id" => $organizations, "pool_type" => 'paid'));
        //}

        exit;
    }

    // end

    public function syncBadgeTopEndorsed() {
        $trophy = $this->Trophy->find("first", array("conditions" => array("base_condition" => "duration", "type" => "received")));
        $fDay = date("Y-m-d 00:00:00", strtotime("first day of this month"));
        $topEndorsedPeople = $this->Endorsement->query("
                                        SELECT * FROM
                                        (
                                            SELECT User.email, User.mongo_id as user_mongo , COUNT(Endorsement.id) as endorsement_count,  YEAR(Endorsement.created) as yearEndorsed, MONTH(Endorsement.created) as monthEndorsed, Endorsement.*
                                            FROM endorsements AS Endorsement 
                                            LEFT JOIN users AS User ON (Endorsement.endorsed_id = User.id) 
                                            WHERE Endorsement.created < '" . $fDay . "' 
                                            GROUP BY YEAR(Endorsement.created), MONTH(Endorsement.created) , Endorsement.endorsed_id , Endorsement.organization_id 
                                        ) AS EndorseTop
                                        LEFT JOIN 
                                        (
                                            SELECT MAX(endorsement_count)  as max_endorsement_count , EndorseNested.organization_id , EndorseNested.monthCreated,  EndorseNested.yearCreated FROM 
                                            (
                                                SELECT COUNT(EndorsementInner.id) endorsement_count  , EndorsementInner.endorsed_id , EndorsementInner.organization_id, MONTH(EndorsementInner.created)  as monthCreated, YEAR(EndorsementInner.created) as yearCreated
                                                FROM endorsements as EndorsementInner 
                                                WHERE EndorsementInner.created < '" . $fDay . "' 
                                                GROUP BY YEAR(EndorsementInner.created), MONTH(EndorsementInner.created) , EndorsementInner.endorsed_id , EndorsementInner.organization_id 
                                            )
                                            AS EndorseNested GROUP BY  EndorseNested.organization_id, yearCreated,  monthCreated
                                        ) AS EndorseMax
                                        ON EndorseTop.endorsement_count  = EndorseMax.max_endorsement_count 
                                        WHERE EndorseTop.organization_id = EndorseMax.organization_id and EndorseTop. yearEndorsed = EndorseMax.yearCreated  and EndorseTop.monthEndorsed =  EndorseMax.monthCreated 
                                        order by  EndorseMax.yearCreated ASC, EndorseMax.monthCreated ASC,  EndorseTop.organization_id
        ");

//        pr($topEndorsedPeople);die;
        $badges = array();
        foreach ($topEndorsedPeople as $topEndorsed) {
            $userId = $topEndorsed['EndorseTop']['endorsed_id'];
            $organizationId = $topEndorsed['EndorseTop']['organization_id'];
            $id = $userId . "_" . $organizationId;
            if (isset($badges[$id])) {
                $badges[$id]['count'] += 1;
            } else {
                $badge = array();
                $badge['user_id'] = $userId;
                $badge['organization_id'] = $organizationId;
                $badge['trophy_id'] = $trophy['Trophy']['id'];
                $badge['count'] = 1;
                $badges[$id] = $badge;
            }
        }

        $this->Badge->saveMany($badges);

        echo "Top Endorsed save";
        exit;
    }

    public function syncBadgeTopEndorser() {
        $trophy = $this->Trophy->find("first", array("conditions" => array("base_condition" => "duration", "type" => "sent")));
        $fDay = date("Y-m-d 00:00:00", strtotime("first day of this month"));

        $topEndorserPeople = $this->Endorsement->query("
                                        SELECT * FROM
                                        (
                                            SELECT User.email, User.mongo_id as user_mongo , COUNT(Endorsement.id) as endorsement_count,  YEAR(Endorsement.created) as yearEndorsed, MONTH(Endorsement.created) as monthEndorsed, Endorsement.*
                                            FROM endorsements AS Endorsement 
                                            LEFT JOIN users AS User ON (Endorsement.endorser_id = User.id) 
                                            WHERE Endorsement.created < '" . $fDay . "' 
                                            GROUP BY YEAR(Endorsement.created), MONTH(Endorsement.created) , Endorsement.endorser_id , Endorsement.organization_id 
                                        ) AS EndorseTop
                                        LEFT JOIN 
                                        (
                                            SELECT MAX(endorsement_count)  as max_endorsement_count , EndorseNested.organization_id , EndorseNested.monthCreated,  EndorseNested.yearCreated FROM 
                                            (
                                                SELECT COUNT(EndorsementInner.id) endorsement_count  , EndorsementInner.endorser_id , EndorsementInner.organization_id, MONTH(EndorsementInner.created)  as monthCreated, YEAR(EndorsementInner.created) as yearCreated
                                                FROM endorsements as EndorsementInner 
                                                WHERE EndorsementInner.created < '" . $fDay . "' 
                                                GROUP BY YEAR(EndorsementInner.created), MONTH(EndorsementInner.created) , EndorsementInner.endorser_id , EndorsementInner.organization_id 
                                            )
                                            AS EndorseNested GROUP BY  EndorseNested.organization_id, yearCreated,  monthCreated
                                        ) AS EndorseMax
                                        ON EndorseTop.endorsement_count  = EndorseMax.max_endorsement_count 
                                        WHERE EndorseTop.organization_id = EndorseMax.organization_id and EndorseTop. yearEndorsed = EndorseMax.yearCreated  and EndorseTop.monthEndorsed =  EndorseMax.monthCreated 
                                        order by  EndorseMax.yearCreated ASC, EndorseMax.monthCreated ASC,  EndorseTop.organization_id
        ");

//        pr($topEndorserPeople);die;
        $badges = array();
        foreach ($topEndorserPeople as $topEndorser) {
            $userId = $topEndorser['EndorseTop']['endorser_id'];
            $organizationId = $topEndorser['EndorseTop']['organization_id'];
            $id = $userId . "_" . $organizationId;
            if (isset($badges[$id])) {
                $badges[$id]['count'] += 1;
            } else {
                $badge = array();
                $badge['user_id'] = $userId;
                $badge['organization_id'] = $organizationId;
                $badge['trophy_id'] = $trophy['Trophy']['id'];
                $badge['count'] = 1;
                $badges[$id] = $badge;
            }
        }
//        pr($badges);die;
        $this->Badge->saveMany($badges);

        echo "Top Endorser save";
        exit;
    }

    // reply notification
    public function replynotify() {
        $params = array();
        $params['fields'] = "Organization.name as orgname,Organization.id as orgid, EndorserUser.id as id, EndorserUser.fname as fname,EndorserUser.lname as lname,EndorsedUser.id as id, EndorsedUser.fname as fname,EndorsedUser.lname as lname, EndorseReplies.reply,EndorseReplies.user_id,EndorseReplies.id";
        $params['joins'] = array(
            array(
                'table' => 'endorsements',
                'alias' => 'Endorsement',
                'type' => 'INNER',
                'conditions' => array(
                    'Endorsement.id = EndorseReplies.endorsement_id',
                )
            ),
//               
//                            array('table' => "login_statistics",
//                                "alias" => "LoginStatistics",
//                                "type" => "INNER",
//                                'conditions' => array(
//                                    'LoginStatistics.user_id =EndorseReplies.user_id AND LoginStatistics.live =1'
//                                )
//                            )
//                       
//				  ,
            array('table' => "organizations",
                "alias" => "Organization",
                "type" => "INNER",
                'conditions' => array(
                    'Organization.id =Endorsement.organization_id'
                )
            )
            ,
            array('table' => "users",
                "alias" => "EndorserUser",
                "type" => "INNER",
                'conditions' => array(
                    'EndorserUser.id =Endorsement.endorser_id'
                )
            )
            ,
            array('table' => "users",
                "alias" => "EndorsedUser",
                "type" => "INNER",
                'conditions' => array(
                    'EndorsedUser.id =Endorsement.endorsed_id'
                )
            )
        );

        $params['conditions'] = array("notification_sent" => 0);
        $notifications = $this->EndorseReplies->find("all", $params);
        //echo $this->EndorseReplies->getLastQuery();
        //print_r($notifications);exit;

        foreach ($notifications as $val) {

            $replyname = $repliedname = "";
            $replyid = 0;
            if ($val["EndorserUser"]["id"] == $val["EndorseReplies"]["user_id"]) {
                $replyname = $val["EndorserUser"]["fname"] . " " . $val["EndorserUser"]["lname"];
                $repliedname = $val["EndorsedUser"]["fname"] . " " . $val["EndorsedUser"]["lname"];
                $replyid = $val["EndorsedUser"]["id"];
            } elseif ($val["EndorsedUser"]["id"] == $val["EndorseReplies"]["user_id"]) {
                $replyname = $val["EndorsedUser"]["fname"] . " " . $val["EndorsedUser"]["lname"];
                $repliedname = $val["EndorserUser"]["fname"] . " " . $val["EndorserUser"]["lname"];
                $replyid = $val["EndorserUser"]["id"];
            }

            $loginuser = $this->LoginStatistics->find("all", array(
                "conditions" => array("user_id" => $replyid, "live" => 1)
            ));


            if (!empty($loginuser[0]["LoginStatistics"]) && $loginuser[0]["LoginStatistics"]["device_id"] != "") {

                if (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "ios") {
                    //print_r($device_token);
                    $deviceToken_msg_arr = array();
                    $token = $loginuser[0]["LoginStatistics"]["device_id"];
                    $count = 1;


                    $organization_id = $val['Organization']['orgid'];
                    //$msg = 'Hi ' . trim($repliedname) . ", you have received a reply from " . trim($replyname) . " from " . $val["Organization"]["orgname"]."\n\n".$val["EndorseReplies"]["reply"];
                    $msg = "You have received a reply to your nDorsement!" . " \n\n" . $val["EndorseReplies"]["reply"];
                    $parameter = array("org_id" => $organization_id, "category" => "SwitchAction", "notification_type" => "post_promotion",
                        "title" => "nDorse App", "is_reply" => 1);

                    $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
                    print_r($deviceToken_msg_arr);
                    echo $this->Common->sendPushNotification($deviceToken_msg_arr);
                } elseif (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "android") {

                    $deviceToken_msg_arr = array();
                    $token = $loginuser[0]["LoginStatistics"]["device_id"];
                    $count = 1;
                    // $end_name = $val['User']['fname'] . " " . $val['User']['lname'];
                    $organization_id = $val['Organization']['orgid'];
                    // $msg = 'Hi ' . trim($repliedname) . ", you have received a reply from " . trim($replyname) . " from " . $val["Organization"]["orgname"]."\n\n<br />".$val["EndorseReplies"]["reply"];
                    $msg = "You have received a reply to your nDorsement!" . "\n\n" . $val["EndorseReplies"]["reply"];
                    $parameter = array("org_id" => $organization_id, "category" => "SwitchAction", "notification_type" => "post_promotion",
                        "title" => "nDorse App", "is_reply" => 1);

                    $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "data" => $parameter);
                    //print_r($deviceToken_msg_arr);
                    echo $this->Common->sendPushNotificationAndroid($deviceToken_msg_arr);
                }
            }
            $this->EndorseReplies->updateAll(array('notification_sent' => 1), array('id' => $val["EndorseReplies"]["id"]));
        }

        echo "Notification sent successfully.";
        exit;
    }

    public function tempEmailcron() {
        ini_set('memory_limit', '2G');
        $params = array(
            'fields' => array(),
            'conditions' => array('mail_sent' => 2),
            'order' => array('TempEmail.created ASC'),
            'limit' => 1
        );

        $emailData = $this->TempEmail->find('all', $params);

//        pr($emailData);die;

        if (!empty($emailData)) {
            $updateEmailIds = array();
            foreach ($emailData as $email) {
                $updateEmailIds[] = $email['TempEmail']['id'];
            }

            $mailSentUpdated = $this->TempEmail->updateAll(array('mail_sent' => 1), array('id' => $updateEmailIds));

            if ($mailSentUpdated) {
                foreach ($emailData as $data) {
                    //                $this->log($data, "registeremaillogs");

                    if (filter_var($data['TempEmail']['to'], FILTER_VALIDATE_EMAIL)) {
                        echo $to = $data['TempEmail']['to'];
                        $subject = $data['TempEmail']['subject'];
                        $viewVars = unserialize($data['TempEmail']['config_vars']);
                        $template = $data['TempEmail']['template'];
                        $bcc = isset($data['TempEmail']['bcc']) ? $data['TempEmail']['bcc'] : array();
                        $cc = isset($data['TempEmail']['cc']) ? $data['TempEmail']['cc'] : array();
                        $attatched = 0;
                        if (isset($viewVars["attatched"]) && ($viewVars["attatched"] == 1)) {
                            $attatched = 1;
                        }

                        $saved = 0;
                        $savedArchive = 0;

                        $archiveData = $data['TempEmail'];
                        $archiveData['email_id'] = $data['TempEmail']['id'];
                        unset($archiveData['id']);

                        $conditions = array();
                        $conditions['LOWER(Archive.to)'] = strtolower($to);
                        $conditions['subject'] = $subject;
                        $conditions['TIMESTAMPDIFF(SECOND,  updated, NOW())  < '] = (24 * 60 * 60);
                        $alreadySentCount = $this->Archive->find("count", array("conditions" => $conditions));
                        //                    echo "<br>" . $this->Archive->getLastQuery();die;
                        //                    pr($alreadySent);die;

                        if ($alreadySentCount > 0) {
                            $this->PendingEmail->clear();
                            $saved = $this->PendingEmail->save($archiveData);
                            if ($saved) {
//                                pr($saved);
                                $deleted = $this->TempEmail->delete($data['TempEmail']['id']);
                                echo "   ---------   Saved in pending emails . Subject : " . $subject;
                            }
                        } else {
                            $this->Archive->clear();
                            $savedArchive = $this->Archive->save($archiveData);

                            if ($savedArchive) {
//                                pr($savedArchive);
                                $deleted = $this->TempEmail->delete($data['TempEmail']['id']);
                                echo "   ---------   Saved in archive emails . Subject : " . $subject;
                                if ($deleted) {
                                    if (trim($template) == "org_admin_access_action") {
                                        $mailSent = $this->orgadminaccessemail($data);
                                        //exit;
                                    } elseif (trim($template) == "org_status_action") {
                                        $mailSent = $this->orgadminaactionemail($data);
                                    } elseif ($attatched == 1) {
                                        $attachments = WWW_ROOT . $viewVars["docs"];
                                        $mailSent = $this->Common->sendEmail($to, $subject, $template, $viewVars, $cc = false, $bcc = false, $attachments);
                                    } else {
                                        $mailSent = $this->Common->sendEmail($to, $subject, $template, $viewVars, $cc = false, $bcc = false, $attachments = false);
                                    }
                                } else {
                                    echo "Invalid Email : " . $data['TempEmail']['to'];
                                    echo "<hr>";

                                    $message = $data['TempEmail']['id'] . " - to : " . $to . " : Email cron - Invalid email";
                                    $this->log($message, "email");

                                    $mailSent = true;
                                }
                            }
                        }
                    }


                    echo "<hr>";
                }
            }

            echo "Email sent successfully.";
            exit;
        } else {
            echo "No email is pending in queue.";
            exit;
        }
    }

    /* Added by Babulal Prasad to send push notification on Scheduled POST * */

    public function postPushNotification() {
        $params = array();
        $params['fields'] = "PostSchedule.id,PostSchedule.post_id,PostSchedule.datetime,FeedTran.id,FeedTran.org_id,FeedTran.visibility_check,FeedTran.visible_dept,FeedTran.visible_sub_org,
        FeedTran.visible_user_ids,FeedTran.publish_date,PostSchedule.status as post_schedule_status,FeedTran.status as feed_trans_status,Post.user_id,Post.title,Post.push_notification,Post.id,Post.email_notification,Post.message";
        $params['joins'] = array(
            array(
                'table' => 'posts',
                'alias' => 'Post',
                'type' => 'LEFT',
                'conditions' => array(
                    'Post.id = PostSchedule.post_id',
                )
            ),
            array('table' => "feed_trans",
                "alias" => "FeedTran",
                "type" => "LEFT",
                'conditions' => array(
                    'FeedTran.feed_id = Post.id',
                    'FeedTran.feed_type = "post"',
                )
            )
        );

        //$params['conditions'] = array("datetime <= " => date("Y-m-d H:i:s", time()),"PostSchedule.status"=>0);
        $params['conditions'] = array("utc_post_datetime <= " => date("Y-m-d H:i:s", time()), "PostSchedule.status" => 0);
        $notifications = $this->PostSchedule->find("all", $params);
//        echo $this->PostSchedule->getLastQuery(); //exit;
        //pr($notifications); exit;
        $deptArray = $subOrgArray = array();
        $this->loadModel('Post');
        $this->loadModel('FeedTrans');
        foreach ($notifications as $index => $postValue) {
            $userList = array();
//            pr($postValue);exit;
            $orgId = $postValue['FeedTran']['org_id'];
            $user_id = $postValue['Post']['user_id'];
            $PostScheduleID = $postValue['PostSchedule']['id'];
            $PostID = $postValue['Post']['id'];
            $FeedTranID = $postValue['FeedTran']['id'];

            $visibility_check = $postValue['FeedTran']['visibility_check'];

            if ($visibility_check == 1) {//if visiblity check is on then send to push notification on given filter
                $userIds = json_decode($postValue['FeedTran']['visible_user_ids'], true);
                if (isset($userIds) && count($userIds) > 0) {
                    foreach ($userIds as $userid) {
                        $userList[$userid] = $userid;
                    }
                }
                $deptIds = json_decode($postValue['FeedTran']['visible_dept'], true);
                if (isset($deptIds) && count($deptIds) > 0) {
                    foreach ($deptIds as $index => $deptID) {
                        $deptArray[$deptID] = $deptID;
                        $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                        $userIDbyDept = $this->UserOrganization->find('all', array("fields" => array("UserOrganization.user_id"), "conditions" => array("department_id" => $deptID, "organization_id" => $orgId, 'status' => 1)));
                        if (isset($userIDbyDept) && count($userIDbyDept) > 0) {
                            foreach ($userIDbyDept as $index => $userOrgData) {
//                                pr($userOrgData['UserOrganization']['user_id']);
                                $userList[$userOrgData['UserOrganization']['user_id']] = $userOrgData['UserOrganization']['user_id'];
                            }
                        }
                    }
                }

                $subOrgIds = json_decode($postValue['FeedTran']['visible_sub_org'], true);
                if (isset($subOrgIds) && count($subOrgIds) > 0) {
                    foreach ($subOrgIds as $index => $subOrgID) {
                        $subOrgArray[$subOrgID] = $subOrgID;
                        $this->UserOrganization->unbindModel(array("belongsTo" => array("Organization", "User")));
                        $userIDbySubOrg = $this->UserOrganization->find('all', array("fields" => array("UserOrganization.user_id"), "conditions" => array("entity_id" => $subOrgID, "organization_id" => $orgId, 'status' => 1)));
                        if (isset($userIDbySubOrg) && count($userIDbySubOrg) > 0) {
                            foreach ($userIDbySubOrg as $index => $userOrgData) {
//                                pr($userOrgData['UserOrganization']['user_id']);
                                $userList[$userOrgData['UserOrganization']['user_id']] = $userOrgData['UserOrganization']['user_id'];
                            }
                        }
                    }
                }
//                echo "<br>ORG : " . $orgId;
//                echo "<br>USER ID : " . $user_id;
//                echo "<br>PostScheduleID: " . $PostScheduleID;

                $orgDATA = $this->Organization->findById($orgId);
                $orgName = $orgDATA['Organization']['name'];
                $userDATA = $this->User->findById($user_id);
                $userName = $userDATA['User']['fname'] . " " . $userDATA['User']['lname'];
                $PostTitle = $postValue['Post']['title'];
                $PostMessage = $postValue['Post']['message'];
                $push_notification = $postValue['Post']['push_notification'];
                $email_notification = $postValue['Post']['email_notification'];

                /* Send email notificatin to user on post publish start* */
                $emailArray = array();
                if (isset($email_notification) && $email_notification == 1) {
                    if (isset($userList) && count($userList) > 0) {
                        foreach ($userList as $userID) {
                            $userData = $this->User->findById($userID);
                            $emailArray[$userID] = $userData['User']['email'];
                        }
                    }
//                    pr($emailArray);

                    if (!empty($emailArray)) {
                        $emailQueue = array();
                        $subject = "nDorse notification -- New Post Submitted";
                        foreach ($emailArray as $uID => $userEmail) {
                            if ($userEmail != '') {

                                $viewVars = array();
                                $viewVars = array("org_name" => $orgName, "post_title" => $PostTitle, 'post_message' => $PostMessage);

                                /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                                $userIdEncrypted = base64_encode($uID);
                                $rootUrl = Router::url('/', true);
                                $rootUrl = str_replace("http", "https", $rootUrl);
                                $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
                                $viewVars["pathToRender"] = $pathToRender;
                                /*                                 * * */
                                $configVars = serialize($viewVars);
                                $emailQueue[] = array("to" => $userEmail, "subject" => $subject, "config_vars" => $configVars, "template" => "post_notification");
                            }
                        }
//                        pr($emailQueue);
                        if (!empty($emailQueue)) {
                            $this->Email->saveMany($emailQueue);
                        }
                    }
                }/* Send email notificatin to user on post publish END* */

//                continue;

                if (isset($push_notification) && $push_notification == 1) {
                    if (isset($userList) && count($userList) > 0) {
                        foreach ($userList as $userID) {
                            $loginuser = $this->LoginStatistics->find("all", array(
                                "conditions" => array("user_id" => $userID, "live" => 1)
                            ));

                            if (!empty($loginuser[0]["LoginStatistics"]) && $loginuser[0]["LoginStatistics"]["device_id"] != "") {
//                                echo "TEST";
//                                pr($loginuser);
//                                exit;
                                if (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "ios") {
                                    //print_r($device_token);
                                    $deviceToken_msg_arr = array();
                                    $token = $loginuser[0]["LoginStatistics"]["device_id"];
                                    $count = 1;
                                    //$msg = 'Hi ' . trim($repliedname) . ", you have received a reply from " . trim($replyname) . " from " . $val["Organization"]["orgname"]."\n\n".$val["EndorseReplies"]["reply"];
                                    $msg = $userName . " has posted a post titled : " . $PostTitle . " \n\n in " . $orgName . " Organization";
                                    $parameter = array("org_id" => $orgId, "category" => "SwitchAction", "notification_type" => "post_promotion", "title" => "nDorse App");

                                    $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "original_msg" => $msg, "data" => $parameter);
//                                    print_r($deviceToken_msg_arr);
                                    echo $this->Common->sendPushNotification($deviceToken_msg_arr);
                                } elseif (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "android") {

                                    $deviceToken_msg_arr = array();
                                    $token = $loginuser[0]["LoginStatistics"]["device_id"];
                                    $count = 1;
                                    // $end_name = $val['User']['fname'] . " " . $val['User']['lname'];
//                                    $organization_id = $val['Organization']['orgid'];
                                    // $msg = 'Hi ' . trim($repliedname) . ", you have received a reply from " . trim($replyname) . " from " . $val["Organization"]["orgname"]."\n\n<br />".$val["EndorseReplies"]["reply"];
                                    $msg = $userName . " has posted a post titled : " . $PostTitle . " \n\n in " . $orgName . " Organization";
                                    $parameter = array("org_id" => $orgId, "category" => "SwitchAction", "notification_type" => "post_promotion",
                                        "title" => "nDorse App");

                                    $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "original_msg" => $msg, "data" => $parameter);
                                    //print_r($deviceToken_msg_arr);
                                    echo $this->Common->sendPushNotificationAndroid($deviceToken_msg_arr);
                                }
                            }
                        }
                    }
                }
            } else { //else send notification to all users of organization
                $orgDATA = $this->Organization->findById($orgId);
                $orgName = $orgDATA['Organization']['name'];
                $userDATA = $this->User->findById($user_id);
                $userName = $userDATA['User']['fname'] . " " . $userDATA['User']['lname'];
                $PostTitle = $postValue['Post']['title'];
                $PostMessage = $postValue['Post']['message'];
                $push_notification = $postValue['Post']['push_notification'];
                $email_notification = $postValue['Post']['email_notification'];
//pr($postValue); exit;
                //if (isset($push_notification) && $push_notification == 1) {
                $this->DefaultOrg->unbindModel(array("belongsTo" => array("Organization", "User")));
                $userIDsOfOrg = $this->DefaultOrg->find('all', array("fields" => array("DefaultOrg.user_id"), "conditions" => array("DefaultOrg.organization_id" => $orgId, 'DefaultOrg.status' => 1)));
                $userList = array();
                if (isset($userIDsOfOrg) && !empty($userIDsOfOrg)) {
                    foreach ($userIDsOfOrg as $index => $userDATAa) {
                        $userList[$userDATAa['DefaultOrg']['user_id']] = $userDATAa['DefaultOrg']['user_id'];
                    }

//                    pr($userList); exit;


                    /* Send email notificatin to user on post publish start* */
                    $emailArray = array();
                    if (isset($email_notification) && $email_notification == 1) {
                        if (isset($userList) && count($userList) > 0) {
                            foreach ($userList as $userID) {
                                $userData = $this->User->findById($userID);
                                $emailArray[$userID] = $userData['User']['email'];
                            }
                        }
//                        pr($emailArray);
//                        exit;
                        if (!empty($emailArray)) {
                            $emailQueue = array();
                            $subject = "nDorse notification -- New Post Submitted";
                            foreach ($emailArray as $uID => $userEmail) {
                                if ($userEmail != '') {

                                    $viewVars = array();
                                    $viewVars = array("org_name" => $orgName, "post_title" => $PostTitle, 'post_message' => $PostMessage);

                                    /** added by Babulal Prasad at @8-feb-2018 for unsubscribe from email */
                                    $userIdEncrypted = base64_encode($uID);
                                    $rootUrl = Router::url('/', true);
                                    $rootUrl = str_replace("http", "https", $rootUrl);
                                    $pathToRender = $rootUrl . "unsubscribe/" . $userIdEncrypted;
                                    $viewVars["pathToRender"] = $pathToRender;
                                    /*                                     * * */
                                    $configVars = serialize($viewVars);
                                    $emailQueue[] = array("to" => $userEmail, "subject" => $subject, "config_vars" => $configVars, "template" => "post_notification");
                                }
                            }
                        
                            if (!empty($emailQueue)) {
                                $this->Email->saveMany($emailQueue);
                            }
                        }
                    }/* Send email notificatin to user on post publish END* */






                    if (isset($push_notification) && $push_notification == 1) {

                        if (isset($userList) && count($userList) > 0) {
                            foreach ($userList as $userID) {
                                $loginuser = $this->LoginStatistics->find("all", array(
                                    "conditions" => array("user_id" => $userID, "live" => 1)
                                ));
                                if (!empty($loginuser[0]["LoginStatistics"]) && $loginuser[0]["LoginStatistics"]["device_id"] != "") {
                                    if (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "ios") {
                                        //print_r($device_token);
                                        $deviceToken_msg_arr = array();
                                        $token = $loginuser[0]["LoginStatistics"]["device_id"];
                                        $count = 1;
                                        //$msg = 'Hi ' . trim($repliedname) . ", you have received a reply from " . trim($replyname) . " from " . $val["Organization"]["orgname"]."\n\n".$val["EndorseReplies"]["reply"];
                                        $msg = $userName . " has posted a post titled : " . $PostTitle . " \n\n in " . $orgName . " Organization";
                                        $parameter = array("org_id" => $orgId, "category" => "SwitchAction", "notification_type" => "post_promotion", "title" => "nDorse App");

                                        $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "original_msg" => $msg, "data" => $parameter);
//                                                    pr($deviceToken_msg_arr);
//                                                    exit;
                                        $this->Common->sendPushNotification($deviceToken_msg_arr);
                                    } elseif (strtolower($loginuser[0]["LoginStatistics"]["os"]) == "android") {

                                        $deviceToken_msg_arr = array();
                                        $token = $loginuser[0]["LoginStatistics"]["device_id"];
                                        $count = 1;
                                        // $end_name = $val['User']['fname'] . " " . $val['User']['lname'];
//                                            $organization_id = $val['Organization']['orgid'];
                                        // $msg = 'Hi ' . trim($repliedname) . ", you have received a reply from " . trim($replyname) . " from " . $val["Organization"]["orgname"]."\n\n<br />".$val["EndorseReplies"]["reply"];
                                        $msg = $userName . " has posted a post titled : " . $PostTitle . " \n\n in " . $orgName . " Organization";
                                        $parameter = array("org_id" => $orgId, "category" => "SwitchAction", "notification_type" => "post_promotion",
                                            "title" => "nDorse App");

                                        $deviceToken_msg_arr[] = array('token' => $token, 'count' => $count, 'msg' => $msg, "original_msg" => $msg, "data" => $parameter);
                                        //print_r($deviceToken_msg_arr);
                                        $this->Common->sendPushNotificationAndroid($deviceToken_msg_arr);
                                    }
                                }
                            }
                        }
                    }
                }
                //}
            }

            echo "<hr>";
            echo "<br>PostScheduleID: " . $PostScheduleID;
            echo "<br>PostID : " . $PostID;
            echo "<br>FeedTranID: " . $FeedTranID;

            $this->PostSchedule->updateAll(array('status' => 1), array('id' => $PostScheduleID));
            $this->Post->updateAll(array('status' => 1), array('id' => $PostID));
            $this->FeedTrans->updateAll(array('status' => 1), array('id' => $FeedTranID));
        }

        exit;
        echo "Posts have been posted successfully.";
        exit;
    }

    /* end send push notification on POST * */
}

?>
