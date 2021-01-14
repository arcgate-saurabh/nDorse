<?php

App::uses('Component', 'Controller');
App::uses('CakeEmail', 'Network/Email');

class ViewContComponent extends Component {

    public $components = array('Session', 'Image', 'Auth');
    
     /**
     * This function is used to encode the string.
     * @param string $data
     * @return string
     */
    public function encodeString($data) {
        if($data) {
            return base64_encode(base64_encode($data));
        }
    }
    
    /**
     * This function is used to decode the string.
     * @param string $data
     * @return string
     */
    public function decodeString($data) {
        if($data) {
            return base64_decode(base64_decode($data));
        }
    }
}
?>