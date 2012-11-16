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
$shorcuts = array(
':all' => '(.*)',
':word' => '(\w+)',
':number' => '([0-9]+)',
':segment'=>'[^/]*',
':alpha' => '([a-zA-Z0-9-_]+)',
);

#Hold in pattern route.
$routes = array(
'users/:number'=>'fire this one!',
'users/:word/:number'=>'fire this one word!',
'all-users/:word/:number/:all'=>'fire this one word!',
'users/:word/:pepe'=>'named parameter',
'blog/:year/:month/:id' => 'blog/entry',
':all'=> 'THis is the widemouth one!'
);
$discovered_handler = "NOT FOUND";
$regex_matches = array();
foreach($routes as $pattern => $handler)
{
    $op = $pattern;
    #Replace named regexps.
    $pattern = strtr($pattern, $shorcuts);
    #collect named params
    // preg_match('~:([\w-]+)~', $uri, $params, PREG_OFFSET_CAPTURE);
    preg_match_all('@:([\w]+)@', $pattern, $params, PREG_PATTERN_ORDER);
    $params = $params[0];
    echo "---<br/>Params are: <br/>";
    echo print_r($params)."<br/>";
    $_clean_replace = function($m)
{
    "<br/>:::::::::::::::::<br/>".print_r($m)."<br/>:::::::::::::::::";
    
    $key = $m[1];
    return "(?P<{$key}>.+?)";
    return '(?P<'.$m[1].'>.+?)';
};
    echo ":: params: ". preg_replace('#(?<!\[\[):([a-z\_]+)(?!:\]\])#uD', '(?P<$0>.+?)', $pattern)."<br/>";
    echo "params: ". preg_replace_callback('#(?<!\[\[):([a-z\_]+)(?!:\]\])#uD', $_clean_replace, $pattern)."<br/>";
    // preg_match_all('~:([\w]+)~', $uri, $p_names, PREG_PATTERN_ORDER);
    // $p_names = $p_names[0];
    // echo "Param names are: ".$p_names;
    
    #Fix named parameters.
    // $pattern = preg_replace('~(:([\w-]+))~', '([\\w-]+)', $pattern);
    $pattern = preg_replace('~(:([\w-]+))~', "(?P<$2>.+?)", $pattern);
    echo "---<br/>Pattern is: {$op} => {$pattern}<br/>";
    
    if (preg_match('#^/?' . $pattern . '/?$#', $uri, $matches)) {
        $discovered_handler = $handler;
        $regex_matches = $matches;
        break;
    }
}



$slim = new SlimG();

$slim->render();

echo "<br/><ul>";
echo "<li><a href='/users'>Users empty</a></li>";
echo "<li><a href='/users/'>Users empty trailing slash</a></li>";
echo "<li><a href='/users/32'>Users 32</a></li>";
echo "<li><a href='/users/view/32'>Users action id</a></li>";
echo "<li><a href='/users/named/parameter'>Users named param</a></li>";
echo "</ul><hr/>";

echo "Pattern is ".htmlentities($pattern)."<br/>";
echo "Handler is {$discovered_handler}<br/>Matches: ";
echo print_r($regex_matches)."<br/><hr/><hr/>";

$r = new Router($uri); // create router instance 

$r->map('/', array('controller' => 'home')); // main page will call controller "Home" with method "index()"
$r->map('/users(/:number)?', array('controller' => 'auth', 'action' => 'login'));
$r->map('/users/:word/:number', array('controller' => 'auth', 'action' => 'logout'));
$r->map('all-users/:word/:number/:all', array('controller' => 'auth', 'action' => 'signup'));
$r->map('/users/:word/:pepe', array('controller' => 'profile')); // will call controller "Profile" with dynamic method ":action()"
$r->map('blog/:year/:month/:id', array('controller' => 'users')); // define filters for the url parameters
$r->map('/users/:id', array('controller' => 'users')); // define filters for the url parameters
 
// $r->default_routes();
$r->run();

echo "Controller: ".$r->controller."<br/>";
echo "Action: ".$r->action."<br/>";
echo "Id: ".$r->id."<br/><hr/>";
print_r($r->params)."------<br/>";

$search = str_replace(array(
            ':any',
            ':alnum',
            ':num',
            ':alpha',
            ':segment',
        ), array(
            '.+',
            '[[:alnum:]]+',
            '[[:digit:]]+',
            '[[:alpha:]]+',
            '[^/]*',
        ), "blog/:year/:month/:day");

$clean = preg_replace('#(?<!\[\[):([a-z\_]+)(?!:\]\])#uD', '(?P<$0>.+?)', $search);
$clean = str_replace(":", "", $clean);
echo "#Clean is ".htmlentities($clean);
echo "<br/>Search: {$search}<br/>";
echo preg_match('#^blog/(?P<year>.+?)/(?P<month>\d{2})/(?P<day>\d{1})$#', "blog/2010/11/1", $params2)."<br/>";
echo print_r($params2);
