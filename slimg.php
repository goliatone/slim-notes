<?php 

class SlimG 
{
    public function __construt()
    {
        
    }   
    
    
    public function render()
    {
        echo "Hola mundo!";
    }
}

class Router {
    public $request;
    public $routes;
    public $controller, $controller_name;
    public $action, $id;
    public $params;
    public $route_found = FALSE;
    
    public $defaults;
    
    public function __construct($url)
    {
        $this->reset($url);
    }
    
    public function reset($url)
    {
        if(($pos = strpos($url, '?'))) $url = substr($url, 0, $pos);
        $this->request = $url;
        $this->routes = array();
        $this->defaults = array(
            'controller' => 'home',
            'action' =>'list',
            'id'=>NULL
        );
    }
    
    public function map($rule, $target = array(), $conditions = array())
    {
        $this->routes[$rule] = new Route($rule, $this->request, $target, $conditions);
    }
    public function set_route($route)
    {
        $this->route_found = TRUE;
        echo "*************************************<br/>Params: ";
        echo print_r($route->params)."<br/>Target: ";
        echo print_r($route->target)."<br/>";
        
        
        $params = $this->params = $route->params;
        
        if(isset($route->params['controller'])) $this->controller = $route->params['controller'];
        if(isset($route->params['action'])) $this->action = $route->params['action'];
        if(isset($route->params['id'])) $this->id = $route->params['id'];
        
        if(count($_GET))
            $this->params = array_merge($params, $_GET);
        
        if(empty($this->controller)) $this->controller = $this->defaults['controller'];
        if(empty($this->action)) $this->action = $this->defaults['action'];
        if(empty($this->id)) $this->id = $this->defaults['id'];
        
        $w = explode('_', $this->controller);
        foreach($w as $k => $v) $w[$k] = ucfirst($v);
        $this->controller_name = implode('',$w);
        
        echo "*************************************<br/>";
    }
    
    public function setProps($key, $default)
    {
        // if(is_strin)
    }
    
    public function getParam($key, $default = NULL)
    {
        
    }
    
    public function default_routes()
    {
        $this->map('/:controller');
        $this->map('/:controller/:action');
        $this->map('/:controller/:action/:id');
    }
    
    public function run()
    {
        // $this->default_routes();
           
        foreach($this->routes as $route)
        {
            if($route->matches)
            {
                $this->set_route($route);
                break;
            }
        }
    }
}

class Route {
    public $shorcuts;        
    public $matches = FALSE;
    public $params;
    public $url;
    private $_conditions;
    private $_target;
    
    public function __construct($url, $request, $target, $conditions)
    {
        $this->reset($url, $conditions, $target);
        $this->compile($url, $request);
    }
    
    public function compile($url, $request)
    {
        echo "..................<br/>Compile!<br/>";
        
        $names  = array();
        $values = array();
        
        $url = strtr($url, $this->shorcuts);
        
        preg_match_all('#:([\w+]+)#', $url, $names, PREG_PATTERN_ORDER);
        $names = $names[0];
        
        #### TRANSFORMATIONS:
        #TODO: Allow for <name:options> => (?P<name>options)
        #TODO: Allow for optional /:controller(/:action) => /:controller(/:action)?
        $callback = array($this,'_clean_pattern');
        $pattern = preg_replace_callback('#:([\w-]+)#', $callback, $url);//.'/?';
        
        if(preg_match("#^\/?{$pattern}\/?$#", $request, $values))
        {
            echo "<br/>".print_r($values)."<br/>";     
            array_shift($values);
            echo "<br/>".print_r($values)."<br/>";
            
            foreach($names as $index => $value){
                echo "KK: {$index}:{$value}<br/>";
                $this->params[$value] = urldecode($values[$index]);}
            foreach($this->target as $key => $value)
                $this->params[$key] = $value;
            
            $this->matches = TRUE;
        }
    }
    
    public function reset($url, $conditions = array(), $target=array())
    {
        $this->url = $url;
        $this->params = array();
        $this->target = $target;
        $this->_conditions = $conditions;
        $this->shorcuts = array(
            ':all' => '(.*)',
            ':word' => '(\w+)',
            ':number' => '([0-9]+)',
            ':segment'=>'[^/]*',
            ':segments' => '[a-z0-9\-\_\/]+',
            ':alpha' => '([a-zA-Z-_]+)',
        );
    }
    
    private function _clean_pattern($matches)
    {   
        $key = $matches[1];
        
        $conditions = "[a-zA-Z0-9_\-\.\!\~\*\\\'\(\)\:\@\&\=\$\+,%]+";
        
        if(array_key_exists($key, $this->_conditions))
        {
            $conditions = $this->_conditions[$key];
        }
        
        $out = "(?P<{$key}>{$conditions})";  
        return $out;
    }
    
}
