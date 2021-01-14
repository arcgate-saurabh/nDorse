<?php
$data = array(
    "auth_users" => $authUser,
    "textcenter" => "Organizations",
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

<!--<div class="row" style="border-bottom:1px solid #fff; padding:20px 0" >
  <div class="col-md-2"> 
      <img width="225" alt="" class="img-responsive" src="/ndorse_dev/img/img1.png">
      <img width="40" alt="" class="img-responsive smiley" src="/ndorse_dev/img/sad.png">
  </div>
  <div class="col-md-3 comp-name">
    <h2><a href="javascript.void(0);">Arcgate Info</a></h2>
    <h3>Arc Info</h3>
<!--<div class="discription"><b>Owner Name :</b> Javed Ahmed Sheikh </div>
<div class="discription">Lorem Ipsum is simply dummy text of the printing and typesetting industry. </div>
</div>
<div class="col-md-4 org-status">
  <div>
    <button class="btn btn-xs btn-info" >Renew Subscription</button>
    <button class="btn btn-xs btn-danger" >Terminate Subscription</button>
  </div> 
  
    <h3>Organization Status: Active</h3>
    <table>
      <tr>
        <td>Total Users:</td>
        <td>4</td>
      </tr>
      <tr>
        <td>Invitation Sent:-</td>
        <td>4</td>
      </tr>
      <tr>
        <td>Via Mobile App:</td>
        <td>4</td>
      </tr>
      <tr>
        <td>Via website:</td>
        <td>4</td>
      </tr>
      <tr>
        <td>Invitation Accepted:</td>
        <td>4</td>
      </tr>
    </table>
</div>
<div class="col-md-3">
<div class="pull-right"><a class="dots" rel="1_one" href="javascript:void(0);"> <img alt="" src="/ndorse_dev/img/3dots.png"> </a></div>
<div class="clearfix"></div>
<div class="nDorse-of-month">
  <h4>Endorsements for the month</h4>
  <h3>0</h3>
</div>
</div>
</div>
<br />
<br /> -->

<!-- Page Content -->
<div class="btn-group" data-toggle="buttons" style="margin-left: -13px; margin-bottom: 10px;">
    <label class="btn btn-primary active orgfilterradio">
        <input type="radio" name="orgtype" id="all" value="all" autocomplete="off" checked> All
    </label>
    <label class="btn btn-primary orgfilterradio">
        <input type="radio" name="orgtype" id="trial" value="trial" autocomplete="off"> Trial
    </label>
    <label class="btn btn-primary orgfilterradio">
        <input type="radio" name="orgtype" id="subsription" value="subscription" autocomplete="off"> Subscription
    </label>
    <label class="btn btn-primary orgfilterradio">
        <input type="radio" name="orgtype" id="nosubsription" value="nosubscription" autocomplete="off"> No Subscription
    </label>
    <label class="btn btn-primary orgfilterradio">
        <input type="radio" name="orgtype" id="inactive" value="inactive" autocomplete="off">  Inactive
    </label>
</div>
<?php if (!empty($orgdata)) { ?>
    <div class="search-icn row">
        <input type="text" class="form-control" id="searchorganization"  placeholder="Filter Items...">
    </div>
<?php } ?>
<div class="clearfix"></div>
<div id="page-content-wrapper" class="row">
    <input type="hidden" id="totalrecords" value="<?php echo $totalrecords; ?>">
    <div class="containerorg lady-lake"> <?php
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
