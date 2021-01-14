<?php

/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
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
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {

    function getLastQuery() {
        $dbo = $this->getDatasource();
        $logs = $dbo->getLog();
        $lastLog = end($logs['log']);
        return $lastLog['query'];
    }

    public function validateImage($params) {
        $allowedMime = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/jpg');
        if (isset($params['image']) && $params['image'] != "") {
             $uri = 'data://application/octet-stream;base64,' . $params['image'];
            $imageInfo = getimagesize($uri);
            if (in_array($imageInfo['mime'], $allowedMime)) {
                $imageName = explode("/", $imageInfo['mime']);
                $this->data[$this->alias]['file_extension'] = $imageName[1];
                $this->data[$this->alias]['imageData'] = $params['image'];
                $this->data[$this->alias]['image'] = "default.jpg";
                return true;
            } else {
                return 'Sorry! only jpg, jpeg, png, gif formats are accepted.';
            }
        } else {
            return true;
        }
    }

}
