<?php
echo $this->Element("commonModal");
?>

<div class="container"> <?php echo $this->Form->create('User',array('class'=> 'form-signin')); ?> <?php echo $this->Session->flash('auth'); ?>
  <p><?php echo $this->Session->Flash(); ?></p>
  <h2 class="form-signin-heading">nDorse Web Admin & Reporting </h2>
  <div align="center" class="ndrose"><?php echo $this->Html->image('logo.png'); ?></div>
  <label class="error"><?php echo $errormsg; ?></label>
  <label for="inputEmail" class="sr-only">Email address</label>
  <?php echo $this->Form->Input("email", array("class" => "form-control", "placeholder" => "Email", "label" => false));?>
<!--  <input type="email" id="inputEmail" name="data[User][email]" value="<?php //echo $username;?>" class="form-control" placeholder="Email"  autofocus>-->
  
  <?php echo $this->Form->Input("password", array("class" => "form-control", "placeholder" => "Password", "label" => false));?>
<!--  <input type="password" id="inputPassword" name="data[User][password]" value="<?php //echo $this->request->data["User"]["password"]?>" class="form-control" placeholder="Password" >-->
  <br>
  <button class="btn btn-lg btn-primary btn-block" type="submit">Log in</button>
    <?php echo $this->Form->checkbox('rememberme', array('class' => 'css-checkbox', "hiddenField" => FALSE, 'label' => false)); ?>
    <label class="css-label" for="UserRememberme">Remember Me</label>
    <div class="text-right"><a href="<?php echo Router::url('/', true) . "users/forgotPassword"; ?>" module="users" id="forgotPassword">Forgot Password?</a></div>
    <?php // echo $this->Form->input('rememberme', array('label' => array('class' => 'css-label'), 'type' => 'checkbox', "checked" => "checked",  'class' => 'css-checkbox'));?>
  <div align="center" class="faq"><?php echo $this->Html->Link("FAQ", array('controller' => 'users', 'action' => 'usersfaq'));?></div>
  <?php echo $this->Form->end();?> </div>
