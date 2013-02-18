<?php 
require 'router.php';
require 'route.php';
require 'view.php';

class FlatG {
    
    static public $config = array();
    
    static public $router;
    
    static public function initialize(&$config)
    {
        self::$config = $config;
        
        //TODO: make this for realz.
        self::$router = new Router();
        self::$router->setBasePath($config['router']['basePath']);
    }
    
    static public function map($routeUrl, $target = '', array $args = array())
    {
        self::$router->map($routeUrl, $target, $args);
        return self::$router;
    }
    
    static public function run()
    {
        $route = FlatG::$router->matchCurrentRequest();
        
        if($route)
        {
            $callback = $route->getTarget();
            if(is_callable($callback)) $callback($route->getParameters());
        } else {
            // $view = self::$config['404'];
            self::render('404', array());
        }
    }
    
    static public function render($name, $data, $layout = FALSE, $return = FALSE)
    {
        //get main content.
        $output = self::renderView($name,$data);
        
        if(!$layout) $layout = self::$config['layout'];
        
        $output = self::renderView($layout, array('content'=>$output));
        
        if($return) return $output;
        else echo $output;
    }
    
    static public function renderView($name, $data)
    {
        $dir  = self::$config['view_dir'];
        $view = new View($name);
        $view->setViewDirectory($dir);
        
        return $view->render($data);
    }
    
    static public function assetUri($asset)
    {
        echo self::$config['asset_path'].$asset;
    }
    
    static public function version()
    {
        echo "FlatG v0.0.1";
    }
    
    /**
     * Compiles an array of HTML attributes into an attribute string and
     * HTML escape it to prevent malformed (but not malicious) data.
     *
     * @param array $a the tag's attribute list
     * @return string
     */
    static public function attr($array)
    {
        $h = '';
        foreach((array)$array as $k => $v) $h .= " $k=\"$v\"";
        return $h;
    }
    
    /**
     * The magic call static method is triggered when invoking inaccessible
     * methods in a static context. This allows us to create tags from method
     * calls.
     *
     *     Html::div('This is div content.', array('id' => 'myDiv'));
     *
     * @param string $tag The method name being called
     * @param array $args Parameters passed to the called method
     * @return string
     */
    static public function __callStatic($tag, $args)
    {
        $args[1] = isset($args[1]) ? self::attr($args[1]) : '';
        return "<$tag{$args[1]}>{$args[0]}</$tag>\n";
    }
}
