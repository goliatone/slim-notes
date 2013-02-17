<?php
require '../lib/k_router.php';
require '../lib/route.php';
require '../flatg/view.php';

$path = pathinfo(__FILE__);
        
$config = array(
    'view_dir' => $path['dirname']."/../views/",
    'layout' => 'layout'
    // 'basePath'
);

FlatG::initialize($config);


$router = new Router();

$router->setBasePath('/slimG/router');

$handle_index = function($params){
        echo 'hola!!!!';
};

$router->map('/', $handle_index , array('methods' => 'GET'));


$router->map('/archives/',
             'archives#list', 
             array('methods' => 'GET', 
                   'name' => 'archives_list'
                   )
         );
$router->map('/archives/:year/:month/:day',
             'archives#order', 
             array('methods' => 'GET', 
                   'name' => 'archives_order',
                   'filters' => array('year' => '[\d]{4}',
                                      'month' => '[\d]{1,2}',
                                      'day' => '[\d]{1,2}'
                                     )
             )
        );
        
$router->map('/users/:id/edit/', 
             'users#edit', 
             array('methods' => 'GET', 
                   'name' => 'users_edit', 
                   'filters' => array('id' => '(\d+)')
             ));
             
$router->map('/contact/',
             array('controller' => 'someController', 
                    'action' => 'contactAction'), 
             array('name' => 'contact'));

$callback = function($params){
  echo "<h3>Fuck this shit!</h3>";
  $data = array();
  $data['name'] = $params['slug'];
  $data['footer'] = FlatG::getView('footer', $data);
  echo FlatG::getView('article', $data);  
};
$router->map('/blog/:slug', 
             $callback, 
             array( 'name'=>'blog#post',
                    'filters' => array( 'slug' => '(.*)')
             )
          );

// capture rest of URL in "path" parameter (including forward slashes)
$router->map('/site-section/:path','some#target',array( 'filters' => array( 'path' => '(.*)') ) );

$route = $router->matchCurrentRequest();
?>

<h3>Current URL & HTTP method would route to: </h3>
<?php if($route) { ?>
    <strong>Target:</strong>
    <pre><?php var_dump($route->getTarget()); ?></pre>
    <?php $callback = $route->getTarget();
          if(is_callable($callback)) $callback($route->getParameters());
          ?>
    <strong>Parameters:</strong>
    <pre><?php var_dump($route->getParameters()); ?></pre>
<?php } else { ?>=
    <pre>No route matched.</pre>
<?php } ?>



<h3>Try out these URL's.</h3>
<p><a href="<?php echo $router->generate('users_edit', array('id' => 5)); ?>"><?php echo $router->generate('users_edit', array('id' => 5)); ?></a></p>
<p><a href="<?php echo $router->generate('contact'); ?>"><?php echo $router->generate('contact'); ?></a></p>

<p><a href="<?php echo $router->generate('blog#post', array('slug' => 'this-is-a-test')); ?>"><?php echo $router->generate('blog#post', array('slug' => 'this-is-a-test')); ?></a></p>
<p>
    <a href="<?php echo $router->generate('archives_order', array('year' => 2012, 'month'=>3, 'day'=>17 )); ?>">
        <?php echo $router->generate('archives_order', array('year' => 2012, 'month'=>3, 'day'=>17 )); ?>
    </a>
</p>

<p><form action="" method="POST"><input type="submit" value="Post request to current URL" /></form></p>
<!--p>
    <form action="<?php /*echo $router->generate('users_create')*/; ?>" method="POST">
        <input type="submit" value="POST request to <?php /*echo $router->generate('users_create');*/ ?>" />
    </form>
 </p-->
<p>
    <a href="<?php /*echo $router->generate('users_list');*/ ?>">
        GET request to <?php /*echo $router->generate('users_list');*/ ?>
    </a>
</p>
  
  
   
<?php 
class FlatG {
    
    static public $config = array();
    
    
    static public function initialize(&$config)
    {
        self::$config = $config;
    }
    
    static public function getView($name, $data)
    {
        $dir  = self::$config['view_dir'];
        $view = new View($name);
        $view->setViewDirectory($dir);
        $view->setGlobal('__view', $view );
        
        return $view->render($data);
    }
    
    static public function version()
    {
        echo "FlatG v0.0.1";
    }
}
