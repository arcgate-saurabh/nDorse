<?php 
$loggedinUser = AuthComponent::user();
$termsAccept = isset($loggedinUser['terms_accept']) ? $loggedinUser['terms_accept'] : 0;
$termClass = "";
if(!$termsAccept) {
    $termClass = 'js_checkTerms';
}

echo $this->Element("iframeModal");
?>

<?php if(!empty ($termsMessage)) { ?>
<script type="text/javascript">alertbootbox('<?php echo $termsMessage; ?>');</script>
<?php } ?>

<div class="congratulation">
  <div class="text-center"><img src="<?php echo  Router::url('/', true); ?>img/logo.png" width="125" alt="" /> </div>
  <div class=" col-md-12 text-center">
    <?php if(!isset($loggedinUser['current_org'])) {?>
    <div id="congatsNoOrg">
        <h2>Congratulations! </h2>
        <br />
        <h2>You have successfully created your profile. </h2>
        <br />
        <br />
        <h2>In order to use the app, either you'll<br />
          have to create your organization or<br />
          join an existing organization. </h2>
        <div class="div-center">    <div class="form-group"> <?php echo $this->Html->link("Create an Org", array("controller" => "client", "action" => "createorg", 'full_base' => true), array("class" => "btn btn-orange btn-block " . $termClass));?> </div>
            <div class="form-group"> <?php echo $this->Html->link("Join an Org", array("controller" => "client", "action" => "joinanorganization", 'full_base' => true), array("class" => "btn btn-orange btn-block " . $termClass));?> 
              <!--        <button class="btn btn-orange"><a href="">JOIN AN ORG </a></button>--> 
            </div>
        </div>
    </div>
    <?php } else  {?>
    <div id="congatsOrg">
        <h2>Congratulations! </h2>
        <br />
        <h2>You have successfully created your profile. </h2>
        <br />
        <br />
        <h2>Click to the button to access your organization.<br /><br /></h2>
        <div class="div-center">    
            <div class="form-group"> 
                <?php echo $this->Html->link("Go To Organization", array("controller" => "endorse", "action" => "index", 'full_base' => true), array("class" => "btn btn-orange btn-block " . $termClass));?> 
            </div>
        </div>
    </div>
    <?php } ?>
    <?php if(!$termsAccept) { ?>
    <div class="checkbox div-center js_tncDiv"> 
      <!--                <input type="checkbox"  value="" class="css-checkbox" id="cor08" name="cor08">--> 
      <?php echo $this->Form->checkbox('congratsAcceptTnc', array('class' => 'css-checkbox', "hiddenField" => FALSE, 'label' => false)); ?>
      <label for="congratsAcceptTnc" class="css-label i-accept"><a  class="showInIframe" id="showTerms" href="<?php echo Router::url('/', true); ?>client/tnc">I agree to End User License Agreement</a> </label>
    </div>
    <?php } ?>
  </div>
</div>
