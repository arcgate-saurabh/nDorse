<?php
        $data = array(
            "textcenter" => "Create User",
            "righttabs" => "1"
        );
        echo $this->Element('header', array('data' => $data));
?>
<p><?php echo $this->Session->Flash(); ?></p>
<?php echo $this->Form->create('User'); ?>
<section>
    <div class="row">
        <form class="form-horizontal">
            <section>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus require">Email</div>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->input('email', array('placeholder' => 'Email', 'class' => 'form-control', 'label' => false)); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus">Username</div>
                            </div>
                            <div class="col-md-10">
                            <?php echo $this->Form->input('username', array('placeholder' => 'Username', 'class' => 'form-control','readonly'=>'readonly', 'label' => false)); ?>
                            </div>
                        </div>
                    </div>
                </section>
                <section>
                    <div class="row" id="fnamelname">
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus require">First Name</div>
                            </div>
                            <div class="col-md-10">
                                <?php echo $this->Form->input('fname', array('placeholder' => 'First Name', 'class' => 'form-control', 'label' => false)); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-2">
                                <div class="labelCus require">Last Name</div>
                            </div>
                            <div class="col-md-10">
                            <?php echo $this->Form->input('lname', array('placeholder' => 'Last Name', 'class' => 'form-control', 'label' => false)); ?>
                            </div>
                        </div>
                    </div>
                </section>
            <section>
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-2">
                            <div class="labelCus require">Password</div>
                        </div>
                        <div class="col-md-10">
                            <?php echo $this->Form->input('password', array('placeholder' => 'Password', 'class' => 'form-control', 'label' => false,'type'=>'password')); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-2">
                            <div class="labelCus require">Confirm Password</div>
                        </div>
                        <div class="col-md-10">
                        <?php echo $this->Form->input('confirm_password', array('placeholder' => 'Confirm Password', 'class' => 'form-control', 'label' => false,'type'=>'password')); ?>
                        </div>
                    </div>
                </div>
        </section>
        </form>
    </div>
</section>


<!--<section>
    <div class="row">
        <div class="col-md-6">
            <div class="col-md-2">
                <div class="labelCus" style="padding-top:23px;">Password</div>
            </div>
            <div class="col-md-10">
            <?php echo $this->Form->input('password', array('placeholder' => 'Password', 'class' => 'form-control', 'label' => false,'type'=>'password')); ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="col-md-2">
                <div class="labelCus" style="padding-top:15px;">Confirm Password</div>
            </div>
            <div class="col-md-10">
            <?php echo $this->Form->input('confirm_password', array('placeholder' => 'Confirm Password', 'class' => 'form-control', 'label' => false,'type'=>'password')); ?>
            </div>
        </div>
    </div>
</section>-->

<section class="container-fluid footer-bg">
    <div class="container">
        <div class="row">
            <div class="pull-right">
                <button type="button" class="btn btn-default" id="clientformcancel">Cancel</button>
                <button type="button" class="btn btn-default" id="superAdminFormSubmit">Save</button>
                 <?php echo $this->Form->end();?>
            </div>
        </div>
    </div>
</section>
<?php echo $this->Form->end();?>


