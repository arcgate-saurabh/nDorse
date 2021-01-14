<?php
$data = array(
    "auth_users" => $authUser,
    "textcenter" => "Search Users",
    "righttabs" => "1"
);
$headerpage = ($authUser["role"] == 1) ? 'header' : 'headerorg';
if ($authUser["role"] == 2) {
    $data['auth_users'] = $authUser;
}
echo $this->Element($headerpage, array('data' => $data));

if (isset($alertMsg)) {
    ?>
    <script>alertbootbox("<?php echo $alertMsg; ?>");</script>
    <?php
}
?>


<div class="search-icn row">
    <input type="text" class="form-control" id="searchallusers"  placeholder="Search Users...">
</div>

<div class="clearfix"></div>
<div id="page-content-wrapper" class="row">
    <input type="hidden" id="totalrecords" value="<?php echo $totalrecords; ?>">
    <div class="containerorg lady-lake UserList"> <?php
// print_r($orgdata);
        if (!empty($orgdata)) {
            echo $this->Element("organizationslisting", array("orgdata" => $orgdata));
        } else {
            echo "<div class = 'nodataavailable'>No Data Available.</div>";
            //pr("No Data Available.");
        }
        ?>
    </div>
    <div style="text-align: center"> <?php echo $this->Html->Image("ajax-loader.gif", array("class" => "hiddenloader hidden")); ?> </div>
</div>
<div class="modal fade" id="myModa2_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" align="center">
            <div class="modal-header">
                <button type="button" class="btn btn-default pull-right close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
                <!-- <h4 class="modal-title" id="gridSystemModalLabel">Modal title</h4>--> 
            </div>
            <div class="modal-body">
                <h4 class="modal-title">ARE YOU SURE YOU WANT TO DELETE?</h4>
                <p>This will delete all data of the organization</p>
            </div>
            <div class="modal-footer">
                <div class="text-center">
                    <button id="deleteclick" onclick="" type="button" class="btn btn-primary btn-blue">Yes</button>
                    <button type="button" class="canceldelete btn btn-primary btn-blue">No</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo $this->Element("commonpopupmessage"); ?>
<input id="pagename" value="indexorganizations" type="hidden">
