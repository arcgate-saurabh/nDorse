<?php
        $data = array(
            "textcenter" => "Create User",
            "righttabs" => "1"
        );
        echo $this->Element('header', array('data' => $data));
?>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

  <div class="stats">
    <div class="row bor-bot">
      <h2>Stats</h2>
      <div class="col-md-6">
        <div class="endrosd-month">
          <h2>Total Core Values</h2>
          <h1> <?php echo $totalCoreValues; ?></h1>
        </div>
      </div>
      <div class="col-md-6">
        <div class="endrosd-month">
          <h2>Total nDorsements</h2>
          <h1> <?php echo $totalEndorsements; ?></h1>
        </div>
      </div>
    </div>
    <div class="row ">
      <div class="col-md-6">
        <div class="total-nDorse-org-user">
          <h2>Total nDorse Organizations</h2>
          <table class="table table-responsive">
            <tr>
              <th>
                <div class="endorse-org-headings">
                  <i class="fa fa-check-circle-o active-org-icon" aria-hidden="true"></i>
                  <span class="active-inactive-label">Active</span>
                </div>
              </th>
              <th><div><?php echo $activeOrgs; ?></div></th>
            </tr>
            <tr>
              <td><i class="fa fa-angle-double-right" aria-hidden="true"></i><span class="active-menu-option">Trial</span></td>
              <td><?php echo $trialOrgs; ?></td>
            </tr>
            <tr>
             <td><i class="fa fa-angle-double-right" aria-hidden="true"></i><span class="active-menu-option">Subscription</span></td>
             <td><?php echo $subscribedOrgs; ?></td>
            </tr>
            <tr>
             <td><i class="fa fa-angle-double-right" aria-hidden="true"></i><span class="active-menu-option">No Subscription</span></td>
             <td><?php echo $activeOrgs - ($trialOrgs + $subscribedOrgs); ?></td>
            </tr>
            <tr>
              <th>
                <div class="endorse-org-headings">
                  <i class="fa fa-times-circle-o inactive-org-icon" aria-hidden="true"></i>
                  <span class="active-inactive-label">Inactive</span>
                </div>
              </th>
              <th><div><?php echo $inActiveOrgs; ?></div></th>
            </tr>
            
          </table>
        </div>
      </div>
      <div class="col-md-6">
        <div class="total-nDorse-org-user">
          <h2>Total nDorse Users</h2>
         <table class="table table-responsive" cellspacing="10" cellpadding="10">
            <tr class="">
              <th><div>Active</div></th>
              <th><div><?php echo $totalActiveUsers; ?></div></th>
            </tr>
<!--            <tr>
             <th><div>Evaluation</div></th>
             <th><div>10</div></th>
            </tr>-->
            <tr>
              <th><div>Inactive</div></th>
              <th><div><?php echo $totalInactiveUsers; ?></div></th>
            </tr>
            
          </table>
        </div>
      </div>
    </div>
  </div>

