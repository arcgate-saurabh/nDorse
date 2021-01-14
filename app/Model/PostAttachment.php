<?php

App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class PostAttachment extends AppModel {
    
    public $validate = array(
        'image' => array(
            'ruleValid' => array(
                'rule' => array('validateImage'),
            )
        )
    );   

    function getEmojiByPostId($post_id) {
        $data = $this->find('all', array( 'fields' => array('PostAttachment.name') ,'conditions' => array('PostAttachment.post_id' => $post_id, "type" => "emojis"),'limit'=> 1));
        if(is_array($data[0]['PostAttachment']) && isset($data[0]['PostAttachment']['name'])){
            return $data[0]['PostAttachment']['name'];
        }else{
            return "No emoji found";
        }
    }

}

?>