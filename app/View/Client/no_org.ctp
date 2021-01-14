<div class="congratulation">
    <div class="text-center"><img src="<?php echo  Router::url('/', true); ?>img/logo.png" width="125" alt="" /> </div>
    <div class=" col-md-12 text-center">
      <h2>Congratulations! </h2>
      <br />
      <h2>You have successfully created your profile. </h2>
      <br />
      <br />
      <h2>In order to use the app, either you'll<br />
        have to create your organization or<br />
        join an existing organization. </h2>
      <div class="form-group">
<!--        <button class="btn btn-orange" type="submit">CREATE AN ORG </button>-->
        <?php echo $this->Html->link("JOIN AN ORG", array("controller" => "client", "action" => "joinanorganization"), array("class" => "btn btn-orange"));?>
<!--<button class="btn btn-orange"><a href="">JOIN AN ORG </a></button>-->
      </div>
    </div>
  </div>