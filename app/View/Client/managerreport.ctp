<div class="clientReports">
    <div class="container-fluid panel">
        <?php
        echo $this->Form->Create("daterange", array("method" => "post", "id" => "chartseachform"));
        echo $this->Form->input('organization_id', array('type' => 'hidden', 'id' => 'orgid', 'name' => 'organization_id', 'value' => $organization_id));
        echo $this->Form->input('organization_name', array('type' => 'hidden', 'id' => 'orgname', 'name' => 'organization_name', 'value' => $orgName));
        ?>

        <div class="row">
            <div class="col-sm-3">
                <h4><strong>Manager View Report</strong></h4>
            </div>
            <div class="col-sm-3">
                <h4><strong>Organization : <?php echo $orgName; ?></strong></h4>
            </div>
            <div class="col-sm-6">
                <?php echo $this->Html->link('<span></span>Back to nDorse', array('controller' => 'endorse'), array('class' => 'bkTndorse', 'escape' => false)); ?>
               <!--<a href="#" class="bkTndorse"><span></span>Back to nDorse</a>-->
            </div>
        </div>

        <div class="row">
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Facility</label>
                    <select class="form-control select-report-type select2 select"  name="facility_id" id="facility_id">
                        <option value="0">Select Facility</option>
                        <?php
                        foreach ($subcenterData as $id => $sCenterName) {
                            $selected = "";
                            if ($facility_id == $id) {
                                $selected = "selected ='selected'";
                            }
                            ?>
                            <option <?php echo $selected; ?> value="<?php echo $id; ?>"><?php echo $sCenterName; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Department</label>
                    <select class="form-control select-report-type select2 select" name="department_id" id="department_id">
                        <option value="0">Select Department</option>
                        <?php
                        foreach ($orgDeptArray as $id => $deptName) {
                            $selected = "";
                            if ($departmentId == $id) {
                                $selected = "selected ='selected'";
                            }
                            ?>

                            ?>
                            <option <?php echo $selected; ?> value="<?php echo $id; ?>"><?php echo $deptName; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="select-date setIcon col-md-12">
                    <div class="col-md-3 form-group">
                        <label> From</label>
                        <?php echo $this->Form->input('startdate', array('placeholder' => 'Start Date', 'type' => 'text', 'id' => 'datepicker_start', 'value' => $this->Time->Format($datesarray["startdate"], DATEFORMAT), 'class' => 'form-control date', 'label' => false));
                        ?> </div>
                    <div class="col-md-3 form-group">
                        <label> To</label>
                        <?php echo $this->Form->input('enddate', array('placeholder' => 'End Date', 'type' => 'text', 'id' => 'datepicker_end', 'value' => $this->Time->Format($datesarray["enddate"], DATEFORMAT), 'class' => 'form-control date', 'label' => false)); ?> </div>
                    <div class="col-md-6 ">
                        <button type="button" class="btn btn-default" id="chartsearch">Apply</button>
                        <button type="button" class="btn btn-default" id="exportmanagerreport" style="float: right;">Export to spreadsheet</button>
                    </div>
                </div>
            </div>
        </div>
        <?php echo $this->Form->End(); ?>
    </div>
    <div class="container-fluid panel">
        <div class="clientReportsList">
            <div class="scroll-body row">
                <table class="table table-striped">
                    <thead id="scrollheader" style="background: rgba(0,0,0,0.15);">
                        <tr>
                            <th width="15%" class="headerSortDown" >Name</th>
                            <th width="12%" style="text-align: center;">nDorsement Received</th>
                            <th width="12%" style="text-align: center;">nDorsement Sent</th>
                            <th width="11%" style="text-align: center;">Last Login Date</th>
                            <th width="20%">Department</th>
                            <th width="20%">Facility</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
//                        pr($arrayendorsementdetail);
                        if (!empty($arrayendorsementdetail)) {
                            foreach ($arrayendorsementdetail as $index => $userData) {
//                                pr($userData);
                                ?>
                                <tr>
                                    <td width="15%" class="headerSortDown" ><?php echo $userData['name']; ?></td>
                                    <td width="12%" style="text-align: center;"><?php echo $userData['endorsed']; ?></td>
                                    <td width="12%" style="text-align: center;"><?php echo $userData['endorser']; ?></td>
                                    <td width="11%" style="text-align: center;"><?php echo $userData['last_app_used']; ?></td>
                                    <td width="20%"><?php echo $userData['department']; ?></td>
                                    <td width="20%"><?php
                                        $subcenterName = '';
                                        if (isset($userData['subcenter_short_name']) && $userData['subcenter_short_name'] != '') {
                                            $subcenterName = $userData['subcenter_short_name'];
                                        } else if (isset($userData['subcenter_name']) && $userData['subcenter_name'] != '') {
                                            $subcenterName = $userData['subcenter_name'];
                                        }
                                        echo $subcenterName;
                                        ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<td colspan='6' style='text-align:center;'><strong>No record found.</strong></td>";
                        }
                        ?>


                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
<script>

    $('.select2').select2();
</script>
