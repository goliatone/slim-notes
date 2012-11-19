<?php 
require_once('./lib/slimg.php');
require_once('./tests/vendors/FUnit.php');

$profiler = new Profiler();
$profiler->start();

#ROUTER
$slim = new SlimG();



$r = $slim->router; // create router instance 
$r->addListener("/:controller/:action/:pepe(?:/([\d]{1,4}))?", function($slim) use ($slim){
    echo "This is just maaaagik!<br/>";    
    echo "I've been fucking callbacked!<br/>".$slim->kk;
    print_r($slim->request);
});
$r->addListener('/', function(){
  echo "kk";  
});

$r->get( '/', array('controller' => 'home')); // main page will call controller "Home" with method "index()"
$r->get( 'users', array('controller' => 'user', 'action' => 'actionA'));
$r->get( 'users(/:alpha)?', array('controller' => 'user', 'action' => 'actionB'));
$r->get( '/users/:word/:number/:segment', array('controller' => 'user', 'action' => 'peperone'));
$r->get( 'all-users/:word/:number/:all', array('controller' => 'user', 'action' => 'signup'));
$r->get( '/:controller/:action/:pepe(?:/([\d]{1,4}))?', array('controller' => 'profile')); // will call controller "Profile" with dynamic method ":action()"
$r->get( 'blog/:year/:month/:id', array('controller' => 'users')); // define filters for the url parameters
$r->get( '/users/:id', array('controller' => 'users'), array('id'=>'[\d]{1,4}')); // define filters for the url parameters
 
$r->run();

echo "Controller: ".$r->controller."<br/>";
echo "Action: ".$r->action."<br/>";
echo "Id: ".$r->id."<br/><hr/>";
echo "------<br/>";
print_r($r->params);
echo "------<br/>";

echo  $profiler->stop("Page render", Profiler::TYPE_PLAIN, true);
$slim->render();