<?php

$searchedDepartments = array();
$searchedUsers = $searchResult->users;
$searchedEntities = array();
$selectedDepartments = isset($endorseSelected['department']) ? $endorseSelected['department'] : array();
$selectedUsers = isset($endorseSelected['user']) ? $endorseSelected['user'] : array();
$selectedEntities = isset($endorseSelected['entity']) ? $endorseSelected['entity'] : array();
$ifEmpty = true;
?>
<div class="suggestion ">
    <div class="form-group main-width">
        <?php
//        pr($searchedUsers);
//        exit;
        foreach ($searchedUsers as $record) {
//            pr($record->about);
            if (!in_array($record->id, $selectedUsers)) {
                $ifEmpty = false;
                $searchClass = "";
                $lockIcon = "";
                if ($record->endorse_count >= $endorsementLimit) {
                    $lockIcon = '<span class="locked"><img src="' . Router::url('/', true) . 'img/locked.png" class=""></span>';
                    $lockIcon = str_replace("http", "https", $lockIcon);
                    $searchClass = "js_noAdd";
                } else {
                    $searchClass = "js_searched";
                }
                ?>

        <div class="select-guest" data-id="<?php echo $record->id; ?>" data-type="user">
            <!--<div class="col-md-4 col-sm-4 col-xs-5 text-right" >-->
            <!--                        <?php
//                        $userImage = '/images/user.png';
//                        if (isset($record->image) && $record->image != '') {
//                            echo $this->Html->Image("/uploads/profile/" . $record->image, array("class" => "endorseimage", "alt" => ""));
//                            $userImage = "/uploads/profile/" . $record->image;
//                        } else {
//                            echo $this->Html->Image("/images/user.png", array("class" => "endorseimage", "alt" => ""));
//                        }
//                        echo $this->Form->input('endorse_image', array('id' => 'endorse_image', 'name' => 'endorse_image', 'type' => 'hidden', 'value' => $userImage));
                        
                        ?>
                        </div>-->
            <!--<div class="col-md-8 col-sm-8 col-xs-7">-->
                <?php echo $this->Form->input('endorse_type', array('id' => 'endorse_type', 'name' => 'endorse_type', 'type' => 'hidden', 'value' => 'user')); 
                    echo $this->Form->input('firstname', array('id' => 'endorse_firstname', 'name' => 'endorse_fistname', 'type' => 'hidden', 'value' => $record->fname)); 
                    echo $this->Form->input('lastname', array('id' => 'endorse_lastname', 'name' => 'endorse_lastname', 'type' => 'hidden', 'value' => $record->lname)); 
                    $deptName = (isset($record->department_name)) ? $record->department_name : ""; 
                    $deptID = (isset($record->department_id)) ? $record->department_id : ""; 
                    echo $this->Form->input('department', array('id' => 'endorse_department', 'name' => 'endorse_department', 'type' => 'hidden', 'value' => $deptName)); 
                    echo $this->Form->input('department_id', array('id' => 'endorse_department_id', 'name' => 'endorse_department_id', 'type' => 'hidden', 'value' => $deptID)); 
                
                ?>

            <h2><?php echo $record->name; ?></h2>
<!--                <h3><?php //echo (isset($record->about)) ? $record->about : ""; ?></h3>
                <h4><?php //echo (isset($record->department_name)) ? $record->department_name : ""; ?></h4>-->
            <!--</div>-->
        </div>
                <?php
            }
        }
//        pr($searchedDepartments);
        foreach ($searchedDepartments as $record) {
            if (!in_array($record->id, $selectedDepartments)) {
                ?>
                <?php
                $ifEmpty = false;
                $searchClass = "";
                $lockIcon = "";
                if ($record->endorse_count >= $endorsementLimit) {
                    $lockIcon = '<span class="locked"><img src="' . Router::url('/', true) . 'img/locked.png" class=""></span>';
                    $lockIcon = str_replace("http", "https", $lockIcon);
                } else {
                    $searchClass = "js_searched";
                }
                ?>
        <div class="row select-guest" data-id="<?php echo $record->id; ?>" data-type="department">
            <div class="col-md-4 col-sm-4 col-xs-5 text-right" >
                        <?php 
                        $userImage = "/images/user.png";
                        echo $this->Html->Image("/images/user.png", array("class" => "", "alt" => "")); 
                        echo $this->Form->input('endorse_image', array('id' => 'endorse_image', 'name' => 'endorse_image', 'type' => 'hidden', 'value' => $userImage));
                        echo $this->Form->input('endorse_type', array('id' => 'endorse_type', 'name' => 'endorse_type', 'type' => 'hidden', 'value' => 'department'));
                        ?>
            </div>
            <div class="col-md-8 col-sm-8 col-xs-7">
                <h2><?php echo $record->name; ?></h2>
                <h3>Department</h3>
                <h4></h4>
            </div>
        </div>
                <?php
            }
        }

        foreach ($searchedEntities as $record) {
            if (!in_array($record->id, $selectedEntities)) {
                $ifEmpty = false;
                $searchClass = "";
                $lockIcon = "";
                if ($record->endorse_count >= $endorsementLimit) {
                    $lockIcon = '<span class="locked"><img src="' . Router::url('/', true) . 'img/locked.png" class=""></span>';
                    $lockIcon = str_replace("http", "https", $lockIcon);
                } else {
                    $searchClass = "js_searched";
                }
                ?>
        <div class="row select-guest" data-id="<?php echo $record->id; ?>" data-type="entity">
            <div class="col-md-4 col-sm-4 col-xs-5 text-right" >
                        <?php echo $this->Html->Image("/images/user.png", array("class" => "", "alt" => "")); ?>
            </div>
            <div class="col-md-8 col-sm-8 col-xs-7">
                <h2><?php echo $record->name; ?></h2>
                <h3>Sub Department</h3>
                <h4></h4>
                <!--<h4>ABC Inc.</h4>-->
            </div>
        </div>
                <?php
            }
        }
        ?>
        <?php
        if ($ifEmpty) {
            echo '<div class="no-data-search no-data"><h3>No Data available</h3></div>';
        }
        ?>
        <!--        <div class="btn-close">
                    <div class="form-group">
                        <button class="close-suggestion" type="button" id="">Close</button>
                    </div>
                </div>-->
    </div>
</div>

