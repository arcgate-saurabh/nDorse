<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
	<?php echo $this->Html->charset(); ?>
        
		
		<script type="text/javascript" src="<?php echo Router::url('/', true);?>js/jquery.min.js"></script>
		
		<link rel="stylesheet" type="text/css" href="<?php echo Router::url('/', true);?>css/bootstrap.min.css"/>
		<script type="text/javascript" src="<?php echo Router::url('/', true);?>js/bootstrap.js"></script>
		
     <link rel="stylesheet" type="text/css" href="<?php echo Router::url('/', true);?>css/style.css"/>
	  <script type="text/javascript" src="<?php echo Router::url('/', true);?>js/highcharts.js"></script>
	
    </head>
    <body>
	<?php echo $this->fetch('content'); ?>
      
    </body>
</html>
