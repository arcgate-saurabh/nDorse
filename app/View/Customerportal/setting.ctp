<?php
$data = array(
    "textcenter" => "Customer Portal Setting",
    "righttabs" => "3",
    "orgid" => $orgDetail['Organization']['id']
);
$headerpage = ($authUser["role"] == 1) ? 'header' : 'headerorg';
echo $this->Element($headerpage, array('data' => $data));
//pr($orgDetail['Organization']['image']); exit;
?>
<p><?php echo $this->Session->Flash(); ?></p>
<?php echo $this->Form->create('Organization'); ?>
<div class="stats">
    <div class="row bor-bot">
        <h2><?php echo $orgDetail['Organization']['name']; ?></h2>
    </div>
</div>
<section>
    <div class="row">
        <form class="form-horizontal">
            <section>
                <div class="row" id="fnamelname">
                    <div class="row createEditOrg">
                        <div class="col-lg-12 ">
                            <div class="labelCus require">Customize Company Logo</div>
                            <div class="labelCus" id="endorse_visible_alert" style="color: salmon;">
                                <div style="float: left;height: 60px;"><?php echo $this->Html->image("Alert_Symbol.png", array('height' => "20px", 'width' => "20px")); ?></div>
                                <div>
                                    *This logo will be show on Customer Feedback Portal. It will not reflect original company logo.
                                </div>
                            </div>
                            <div class="col-mt-2">
                                <?php
                                //$orgDetail

                                if (isset($orgDetail)) {
                                    if (isset($orgDetail['Organization']['image']) && $orgDetail['Organization']['image'] != '') {
                                        $org_imagenew = Router::url('/', true) . "app/webroot/" . ORG_IMAGE_DIR . $orgDetail['Organization']['image'];
                                        echo $this->Html->image($org_imagenew, array('width' => '175', 'id' => 'org_image'));
                                    } else {
                                        echo $this->Html->image('comp_pic.png', array('width' => '214', 'id' => 'org_image'));
                                    }
                                }
                                ?>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <button type="button"  id="org_upload_photo" class="btn btn-blue">Upload Picture</button>
                                &nbsp;&nbsp;
                                <!--<button type="button" id="org_remove_photo" class="btn btn-blue">Remove Picture</button>-->
                                <?php
                                echo $this->Form->input('Userphoto', array(
                                    'type' => 'file',
                                    'id' => 'photo',
                                    'label' => false,
                                    'class' => 'btn_uplaod_file hidden'
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section>
                <div class="row">
                    <div class="col-md-6"> 
                        <!--<div class="col-md-1"></div> -->
                        <div class="col-md-3">
                            <div class="labelCus">Show Core Values</div>
                        </div>
                        <div class="col-md-9"> 
                            <span class="radio">
                                <?php
                                $options = array(
                                    '1' => 'Yes',
                                    '0' => 'No'
                                );
                                echo $this->Form->input('cp_show_core_values', array('type' => 'radio',
                                    'separator' => '</div><div>',
                                    'before' => '<div class="col-md-3">',
                                    'after' => '</div>',
                                    'options' => $options,
                                    'label' => true,
                                    'legend' => false,
                                    'value' => $orgDetail['Organization']['cp_show_core_values'],
                                    'class' => 'allowIt',
                                        )
                                );
                                ?>
                            </span> 
                        </div>
                    </div>
                    <!-- added by babulal prasad @28-02-2018 to show/hide leader board -->
                    <div class="col-md-6"> 
                        <!--<div class="col-md-1"></div>--> 
                        <div class="col-md-3">
                            <div class="labelCus" >Show Comment Box</div>
                        </div>
                        <div class="col-md-9"> 
                            <span class="radio">
                                <?php
                                $options = array(
                                    '1' => 'Yes',
                                    '0' => 'No'
                                );
                                echo $this->Form->input('cp_show_comment', array('type' => 'radio',
                                    'separator' => '</div><div>',
                                    'before' => '<div class="col-md-3">',
                                    'after' => '</div>',
                                    'options' => $options,
                                    'label' => true,
                                    'legend' => false,
                                    'value' => $orgDetail['Organization']['cp_show_comment'],
                                        )
                                );
                                ?>
                            </span> 
                        </div>
                    </div>

                </div>
            </section>
        </form>
    </div>
</section>
<section class="container-fluid footer-bg">
    <div class="container">
        <div class="row">
            <div class="pull-right">
                <button type="button" class="btn btn-default" id="clientformcancel">Cancel</button>
                <button type="button" class="btn btn-default" id="superAdminFormSubmit">Save</button>
            </div>
        </div>
    </div>
</section>
<?php echo $this->Form->end(); ?>