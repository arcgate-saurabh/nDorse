<div class="container">
  <div class="download-ndorse"> <?php echo $this->Html->Image("/images/logo.png", array("width" => "100"));?>
    <div class="col-md-12" style="padding:0 0; margin:10px 0 20px;">
      <div class="faq-title ">
        <h2>FAQs re: NDORSE APP</h2>
      </div>
      <div class="clearfix visible-sm visible-xs"></div>
      <div class="faq-search" >
        <input class="form-control" type="text" placeholder="What are you looking for?">
      </div>
    </div>
    <div class="faq-accordion">
      <div class="panel-group">
        <div id="panelid56" class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title"> <a href="#collapse56" data-parent="#accordion" data-toggle="collapse" class="accordion-toggle collapsed"> Why are users not receiving email notifications from nDorse? </a> </h4>
          </div>
          <div class="panel-collapse collapse" id="collapse56">
            <div class="panel-body"> We use sendgrid.com to send email notifications. Please have your IT department  whitelist emails coming from *ndorse.net and *sendgrid.com </div>
          </div>
        </div>
        <div id="panelid55" class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title"> <a href="#collapse55" data-parent="#accordion" data-toggle="collapse" class="accordion-toggle collapsed"> Why can't I login to the admin web app or nDorse web app? </a> </h4>
          </div>
          <div class="panel-collapse collapse" id="collapse55">
            <div class="panel-body"> nDorse Web Applications have been tested on Internet Explorer 11, Chrome, and Firefox. For many corporate environments, the IT department needs to modify the security on Internet Explorer to allow the web admin to work. </div>
          </div>
        </div>
        <div id="panelid53" class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title"> <a href="#collapse53" data-parent="#accordion" data-toggle="collapse" class="accordion-toggle collapsed"> Why can't I login anywhere? </a> </h4>
          </div>
          <div class="panel-collapse collapse" id="collapse53">
            <div class="panel-body"> Your username and password are case-sensitive, so if your username is jdoe@acme.com and you use Jdoe@acme.com, login will fail. In addition, if you have a valid nDorse account but for whatever reason are re-invited to the nDorse system, the current password is the one in the invitation email. </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $this->Element("footersite");?>