<?php 
require_once('./lib/slimg.php');
require_once('./tests/vendors/FUnit.php');

#ROUTER
$base_url = '/slimg/';
$slim = new SlimG($base_url);



$r = $slim->router; // create router instance 
$r->addListener("/:controller/:action/:pepe(?:/([\d]{1,4}))?", function($slim) use ($slim){
    echo "This is just maaaagik!<br/>";    
    echo "I've been just callbacked!<br/>".$slim->name;
    print_r($slim->request);
});
$r->addListener('/', function(){
  echo "kk";  
});
$r->addListener('blog/tags(/:slug)', function(){
    echo "kk";
});

//Routes:
//pages. =====> site controller.
//     |-index.
//     |-about.
//     |-legal.
//     |-open source, project.
//     |-downloads.
//     |-contact.
//blog  ======> yoto controller.
//blog/article-slug
//blog/archives
//blog/archives/year
//blog/archives/year/month
//blog/archives/year/month/day
//api   ======> api controller.
//api/page-slug
//api/article-slug
//api/archives
//api/archives/year
//api/archives/year/month
//api/archives/year/month/day
$r->get('/', function(){return 'pepe';}); // main page will call controller "Home" with method "index()"
$r->get('/:slug', array('controller' => 'site', 'action' => 'page'));
$r->get('blog/:slug', array('controller' => 'yoto', 'action' => 'index'));
$r->get('blog/archives(/:year(/:month(/:day)))', array('controller' => 'yoto', 'action' => 'archives'));
$r->get('blog/tags(/:slug)', array('controller' => 'yoto', 'action' => 'tags'));
$r->get('(<controller>(/<action>(/<id>)))', array('controller' => 'yoto', 'action' => 'index'));

// $r->get('users', array('controller' => 'user', 'action' => 'actionA'));
// $r->get( 'users(/:alpha)', array('controller' => 'user', 'action' => 'actionB'));
// $r->get( '/users/:word/:number/:segment', array('controller' => 'user', 'action' => 'peperone'));
// $r->get( 'all-users/:word/:number/:all', array('controller' => 'user', 'action' => 'signup'));
// $r->get( '/:controller/:action/:pepe(/<id>)', array('controller' => 'profile')); // will call controller "Profile" with dynamic method ":action()"
// $r->get( 'blog/:year/:month/:id', array('controller' => 'users')); // define filters for the url parameters
// $r->get( '/users/:id', array('controller' => 'users'), array('id'=>'[\d]{1,4}')); // define filters for the url parameters
 
$r->run();
$slim->render();

echo "Controller: ".$r->controller."<br/>";
echo "Action: ".$r->action."<br/>";
echo "Id: ".$r->id."<br/><hr/>";
echo "------<br/>";
print_r($r->params);
echo "------<br/>";