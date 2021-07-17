
<div class="row join-org join-org-grp  " >
    <div class="text-center div-center">
        <!--    <div id="flashmessage" class="alert"></div>-->
        <!--    <p id="flashmessage"><?php // echo $this->Session->Flash();       ?></p>-->
        <?php echo $this->Form->Create("Organization", array("onsubmit" => "return false")); ?>
        <h3>Please enter the unique code in the box below to join an organization.</h3>
        <div class="form-group"> <?php echo $this->Form->input("secretcode", array("class" => "form-control", "placeholder" => "Organization Code", "label" => false)); ?> 
          <!--                <input type="text" class="form-control text-center" id="email" name="data['organization']" placeholder="Enter code you recieved" />--> 
        </div>
        <div class="form-group">
            <button class="btn btn-block btn-orange" id="joinorganization" type="submit">Submit </button>
        </div>
        <?php echo $this->Form->end(); ?> </div>
    <div class="col-md-12 text-center"> <img src="<?php echo Router::url('/', true); ?>img/or-join.png" class="img-responsive" alt="" /> </div>
    <div class="text-center div-center" style="position:relative" >
        <h2>Search Organizations</h2>
        <form class="form-inline">
            <div class="form-group" style="position:relative">
                <input type="text" class="form-control enter-org" placeholder="Enter name of organization" id="searchorganization" >
                <button disabled="disabled" type="button" id="clearsearcheddata" class="btn btn-orange btn-close">X</button>
            </div>
            <div id="livesearch"></div>
        </form>
        <!--    <h2 class="recommend">Recommended Organizations</h2>-->
    </div>
    <!--<button class="btn btn-block btn-orange" id="sendmultiplerequest" type="submit">Send Multiple Request </button> -->

    <div class="send-multi hidden"><button data-toggle="tooltip" data-original-title="Submit Here" class="btn btn-primary" id="sendmultiplerequest" type="submit"><b>Send Request</b>
            &nbsp;<span class="badge counterorg"></span>
        </button></div>


</div>
<?php //pr($orgdata["organization"]);?>
<div class="join-org-grp" id ="orglisting">
    <?php
    if (isset($orgdata["organization"])) {
        echo $this->Element("corganizationslisting");
    } else {
        echo "<div class = 'nodataavailable'>$orgdata</div>";
    }
    ?>
</div>
<input type="hidden" name="pagename" id="pagename" value="joinorg">
<div style="text-align: center" class="col-md-offset-2"> <?php echo $this->Html->Image("ajax-loader.gif", array("class" => "hiddenloader hidden")); ?> </div>
<!--Join Org -->
</div>




