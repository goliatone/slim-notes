<?php 
require_once('./slimg.php');

#REQUEST
echo "Request: ".$_SERVER['REQUEST_URI']."<br/>";
# Get actual rerouted URL.
$uri = $_SERVER['REQUEST_URI'];

$request = explode('/', $uri);

$script  = explode('/', $_SERVER['SCRIPT_NAME']);  
echo "Request is:".print_r($request)."<br/>";
echo "Script is: ".print_r($script)."<br/>";

#Get method
//array("GET","POST","PUT","DELETE", "HEAD");
$m = array_key_exists('REQUEST_METHOD', $_SERVER) ? $_SERVER['REQUEST_METHOD'] : NULL;
#Hack on browsers not attaching PUT/DELETE.
if($m == "POST" && array_key_exists('_m', $_POST)) 
    $m = strtoupper($env['POST']['_m']);
echo "Request method is: $m <br/>";

#ROUTER
$slim = new SlimG();

$slim->render();

echo "<br/><ul>";
echo "<li><a href='/users'>Users empty</a></li>";
echo "<li><a href='/users/'>Users empty trailing slash</a></li>";
echo "<li><a href='/users/32'>Users 32</a></li>";
echo "<li><a href='/users/view/32'>Users action id</a></li>";
echo "<li><a href='/users/named/parameter'>Users named param</a></li>";
echo "</ul><hr/>";

$r = new Router($uri); // create router instance 

$r->map('/', array('controller' => 'home')); // main page will call controller "Home" with method "index()"
$r->map('/users(/:alpha)?', array('controller' => 'user', 'action' => 'listing'));
$r->map('/users/:word/:number/:segments', array('controller' => 'user', 'action' => 'peperone'));
$r->map('all-users/:word/:number/:all', array('controller' => 'user', 'action' => 'signup'));
$r->map('/:controller/:action/:pepe', array('controller' => 'profile')); // will call controller "Profile" with dynamic method ":action()"
$r->map('blog/:year/:month/:id', array('controller' => 'users')); // define filters for the url parameters
$r->map('/users/:id', array('controller' => 'users')); // define filters for the url parameters
 
$r->run();

echo "Controller: ".$r->controller."<br/>";
echo "Action: ".$r->action."<br/>";
echo "Id: ".$r->id."<br/><hr/>";
echo "------<br/>";
print_r($r->params);
echo "------<br/>";

