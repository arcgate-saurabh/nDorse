<?php
        $data = array(
            "textcenter" => "Create User",
            "righttabs" => "1"
        );
        echo $this->Element('header', array('data' => $data));
?>

<button onclick="dowloadAllUserList();" class="btn btn-xs btn-blue">Download All Users List</button>
<button onclick="dowloadAllOrganizationsList();" class="btn btn-xs btn-blue">Download Organization List</button>
<div class="clearfix"></div>
<div class="loader" style="display: none;" id="reportsLoader"> </div>
