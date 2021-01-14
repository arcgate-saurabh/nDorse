<?php

/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
Router::parseExtensions('json');

$subdomain = substr( env("HTTP_HOST"), 0, strpos(env("HTTP_HOST"), ".") );
/*if($subdomain === 'api')
{
  Router::connect('/', array('controller' => 'api', 'action' => 'index'));
  Router::connect('/:action/*', array('controller' => 'api'));
}*/

//Router::parseExtensions('csv');
//Router::connect('/', array('controller' => 'users', 'action' => 'index'));
Router::connect('/', array('controller' => 'site', 'action' => 'index'));
//Router::connect('/', array('controller' => 'organizations', 'action' => 'index'));
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
Router::connect('/createclient', array('controller' => 'users', 'action' => 'createclient'));
Router::connect('/createorg', array('controller' => 'users', 'action' => 'createorg'));
Router::connect('/setImage', array('controller' => 'users', 'action' => 'setImage'));
Router::connect('/setOrgImage', array('controller' => 'users', 'action' => 'setOrgImage'));
Router::connect('/setorgcpimage', array('controller' => 'users', 'action' => 'setorgcpimage'));
Router::connect('/faq', array('controller' => 'users', 'action' => 'usersfaq'));
Router::connect('/googlelogin', array('controller' => 'client', 'action' => 'googlelogin'));
Router::connect('/google_login', array('controller' => 'client', 'action' => 'google_login'));
Router::connect('/unsubscribe/:key', array('controller' => 'users', 'action' => 'unsubscribe', 'key' => 'key'));

Router::connect('/guest/index/:id', array('controller' => 'guest', 'action' => 'index', 'id' => 'id'));
Router::connect('/guest/endorse/:id', array('controller' => 'guest', 'action' => 'endorse', 'id' => 'id'));
Router::connect('/guest/thanks/:id', array('controller' => 'guest', 'action' => 'thanks', 'id' => 'id'));

Router::connect('/daisy/index/:id', array('controller' => 'daisy', 'action' => 'index', 'id' => 'id'));
Router::connect('/daisy/endorse/:id', array('controller' => 'daisy', 'action' => 'endorse', 'id' => 'id'));
Router::connect('/daisy/thanks/:id', array('controller' => 'daisy', 'action' => 'thanks', 'id' => 'id'));

Router::connect('/client/managerreport/:id', array('controller' => 'client', 'action' => 'managerreport', 'id' => 'id'));

Router::connect('/post/download/:file/:filename/', array('controller' => 'post', 'action' => 'download', 'file' => 'file', 'filename' => 'filename'));
Router::connect('/sso/:shotcode', array('controller' => 'client', 'action' => 'adfslogin', 'shotcode' => 'shotcode'));
Router::connect('/client/adfsMobileLogin/:query', array('controller' => 'client', 'action' => 'adfsMobileLogin', 'query' => 'query'));
/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
require CAKE . 'Config' . DS . 'routes.php';
require 'site_constant.php';
require 'config_array.php';

