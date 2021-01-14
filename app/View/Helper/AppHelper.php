<?php

/**
 * Application level View Helper
 *
 * This file is application-wide helper file. You can put all
 * application-wide helper-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Helper
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Helper', 'View');

/**
 * Application helper
 *
 * Add your application-wide methods in the class below, your helpers
 * will inherit them.
 *
 * @package       app.View.Helper
 */
class AppHelper extends Helper {

    function commoncorevaluesarrangement($corevalues) {
        //=ordered the arrangemenet  
        $orderedvalue = array();
        foreach ($corevalues as $key => $value) {
            $orderedvalue[$value["name"]] = $value["color_code"];
        }
        ksort($orderedvalue);
        $corevalues = array();
        foreach ($orderedvalue as $name => $colorcode) {
            $corevalues[] = array("name" => $name, "color_code" => $colorcode);
        }
        return $corevalues;
        //=end ordered the arrangemenet
    }

    /*     * * Added by Babulal Prasad to get feed time interval Start** */

    function getFeedTimeInterval($createddate, $servertime, $defaultdate) {
        if (isset($createddate) && isset($servertime) && isset($defaultdate)) {
            $now = new DateTime(date("Y-m-d H:i:s", $servertime));
            $timediff = (array) $now->diff($createddate, true);

            $arraytimediff = array("y" => "year", "m" => "month", "d" => "days", "h" => "hr", "i" => "minute", "s" => "second",);
            foreach ($timediff as $key => $difference) {
                if ($difference != 0) {
                    $diffkey = $arraytimediff[$key];
                    if ($key == "s") {
                        echo "few seconds ago";
                    } elseif ($key == "h" || $key == "i") {
                        $plural = ($difference <= 1) ? "" : "s";
                        echo $difference . " " . $diffkey . $plural . " ago";
                    } else {
                        echo $defaultdate;
                    }
                    break;
                }
            }
        } else {
            return 'Invalid dates';
        }
    }

    /* Added by Babulal Prasad to encode user data in Database @30-Aug-2018 */

    public function encodeData($data) {
        echo $data;
        if (isset($data) && $data != '') {
            return $encodedData = base64_encode($data);
        } else {
            return $data;
        }
    }

    /* Added by Babulal Prasad to decode user data in Database @30-Aug-2018 */

    public function decodeData($data) {
        if (isset($data) && $data != '') {
                return $encodedData = base64_decode($data);
        }else {
            return $data;
        }
    }

    /*     * * Added by Babulal Prasad to get feed time interval End** */
}
