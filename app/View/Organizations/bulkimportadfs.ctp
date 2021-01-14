<script  type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-csv/0.8.9/jquery.csv.js"></script>
<section class="" style="color: white;">
    <div class="col-md-6 col-lg-6">
        <div class="BulkImport header-bg-nav">
            <h4>Bulk Import Of SSO Users</h4>
            <div>
                <input type="file" id="bulkuserbutton" class="hidefileupload hidden">
                <div class="pull-right BrowseButton">
                    <div class="choosefile">
                        <h6 id="choosefilebulkusers">Choose File </h6>
                        <span>
                            <?php if ($orgId == 148) { ?>
                                <input value="Upload" onclick="uploadcsvbulkADFSuser(148, 'ArcGate', '81099')" class="btn btn-default pull-right" id="uploadfile_bulkuser" type="button">
                            <?php } elseif ($orgId == 415) { ?>
                                <input value="Upload" onclick="uploadcsvbulkADFSuser(415, 'LGH', '0009e')" class="btn btn-default pull-right" id="uploadfile_bulkuser" type="button">
                            <?php } elseif ($orgId == 425) { ?>
                                <input value="Upload" onclick="uploadcsvbulkADFSuser(425, 'LCMC', '55924')" class="btn btn-default pull-right" id="uploadfile_bulkuser" type="button">
                            <?php } elseif ($orgId == 426) { ?>
                                <input value="Upload" onclick="uploadcsvbulkNEWLCMCuser(426, 'LCMC', '56b60')" class="btn btn-default pull-right" id="uploadfile_bulkuser" type="button">
                            <?php } ?>
                        </span> 
                        <a href="javascript:void(0)" data-toggle="collapse" data-target="#ToolTip02" aria-expanded="false" aria-controls="ToolTip02">
                            <!--<img src="/ndorse/prod2/img/helpIcon.png" alt="">-->
                        </a> 
                    </div>
                    <div class="collapse Popover" id="ToolTip02">
                        <div class="well">
                            <ul class="list-inline">
                                <li>Step1: Download the empty template (CSV file) from website by clicking on Download template button.</li>
                                <li></li>
                                <li>Step2: Fill in the details in that CSV file. Save it in your computer’s hard drive.</li>
                                <li></li>
                                <li>Step3: Upload that CSV file to server by clicking on “Upload” button.</li>
                            </ul>
                            <span class="pull-right popOverArrow"><img src="/ndorse/prod2/img/popOverArrow.png" alt=""></span> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</section>
<!--<section class="row footer-bg bulk-users">
    <div class="col-md-6 col-lg-6">
        <div class="BulkImport">
            <h4>Bulk Import Of Users</h4>
            <div>
                <div class="pull-left DownloadLink"> <img src="/ndorse/prod2/img/DownloadTemplate.png" alt="" align="left">                    <h5><a href="/ndorse/prod2/organizations/bulkusertemplate">Download Template</a></h5>
                </div>
                <input type="file" id="bulkuserbutton" class="hidefileupload hidden">
                <div class="pull-right BrowseButton">
                    <div class="choosefile">
                        <h6 id="choosefilebulkusers">Choose File </h6>
                        <span>
                            <input value="Upload" onclick="uploadcsvbulkuser(148, 'ArcGate', '81099')" class="btn btn-default pull-right" id="uploadfile_bulkuser" type="button">
                        </span> <a href="javascript:void(0)" data-toggle="collapse" data-target="#ToolTip02" aria-expanded="false" aria-controls="ToolTip02"><img src="/ndorse/prod2/img/helpIcon.png" alt=""></a> </div>
                    <div class="collapse Popover" id="ToolTip02">
                        <div class="well">
                            <ul class="list-inline">
                                <li>Step1: Download the empty template (CSV file) from website by clicking on Download template button.</li>
                                <li></li>
                                <li>Step2: Fill in the details in that CSV file. Save it in your computer’s hard drive.</li>
                                <li></li>
                                <li>Step3: Upload that CSV file to server by clicking on “Upload” button.</li>
                            </ul>
                            <span class="pull-right popOverArrow"><img src="/ndorse/prod2/img/popOverArrow.png" alt=""></span> </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-md-6 col-lg-6">
        <div class="BulkImport">
            <h4>Bulk Import Of Photo Of Existing Users</h4>
            <div class="">
                <div class="pull-left DownloadLink"> <img src="/ndorse/prod2/img/DownloadTemplate.png" alt="" align="left">                    <h5><a href="/ndorse/prod2/organizations/bulklinkimportstemp/148">Download Template</a></h5>
                </div>
                <input type="file" id="bulkimagesbutton" class="hidefileuploadimages hidden">
                <div class="pull-right BrowseButton">
                    <div class="choosefile">
                        <h6 id="choosefileexistingusers">Choose File </h6>
                        <span>
                            <input value="Upload" onclick="uploadbulkimages(148)" class="btn btn-default pull-right" id="" type="button">
                        </span> <a href="javascript:void(0)" data-toggle="collapse" data-target="#ToolTip01" aria-expanded="false" aria-controls="ToolTip01"><img src="/ndorse/prod2/img/helpIcon.png" alt=""></a> </div>
                    <div class="collapse Popover" id="ToolTip01">
                        <div class="well">
                            <ul class="list-inline">
                                <li>Step1: Download the CSV file of existing users from website by clicking on Download template button.</li>
                                <li></li>
                                <li>Step2: Fill in the links for each email id in that CSV file. Save it in your computer’s hard drive.</li>
                                <li></li>
                                <li>Step3: Upload that CSV file to server by clicking on “Upload” button.</li>
                            </ul>
                            <span class="pull-right popOverArrow"><img src="/ndorse/prod2/img/popOverArrow.png" alt=""></span> </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</section>-->

<div class="modal fade" id="myModalbulkusersimports" role="dialog">
    <div class="modal-dialog"> 

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" >
                <button type="button" class="btn btn-default pull-right close closebulkimport" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Users Import Status</h4>
            </div>
            <div class="modal-body" >
                <div id="bulkuserstable"> </div>
            </div>
        </div>
    </div>
</div>
