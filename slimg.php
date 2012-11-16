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
    
    public $default_id = NULL;
    public $default_action = 'index';
    public $default_controller = 'home';
    
    public function __construct($url)
    {
        $this->reset($url);
    }
    
    public function reset($url)
    {
        if(($pos = strpos($url, '?'))) $url = substr($url, 0, $pos);
        $this->request = $url;
        $this->routes = array();
    }
    
    public function map($rule, $target = array(), $conditions = array())
    {
        $this->routes[$rule] = new Route($rule, $this->request, $target, $conditions);
    }
    public function set_route($route)
    {
        $this->route_found = TRUE;
        echo "*************************************<br/>";
        print_r($route->params)."<br/>";
        print_r($route->target)."<br/>";
        
        
        $params = $this->params;
        
        if(isset($route->params['controller'])) $this->controller = $route->params['controller'];
        if(isset($route->params['action'])) $this->action = $route->params['action'];
        if(isset($route->params['id'])) $this->id = $route->params['id'];
        
        echo "Params: ".$params['controller']."<br/>";
        echo "Controller: ".$this->controller."<br/>";
        
        // unset($params['controller']);    
        // unset($params['action']);    
        // unset($params['id']);
        
        if(count($_GET))
            $this->params = array_merge($params, $_GET);
        
        if(empty($this->controller)) $this->controller = $this->default_controller;
        if(empty($this->action)) $this->action = $this->default_action;
        if(empty($this->id)) $this->id = $this->default_id;
        
        $w = explode('_', $this->controller);
        foreach($w as $k => $v) $w[$k] = ucfirst($v);
        $this->controller_name = implode('',$w);
        
        echo "*************************************<br/>";
    }
    
    
    public function default_routes()
    {
        $this->map('/:controller');
        $this->map('/:controller/:action');
        $this->map('/:controller/:action/:id');
    }
    
    public function run()
    {
        foreach($this->routes as $route)
        {
            if($route->is_match)
            {
                $this->set_route($route);
                break;
            }
        }
    }
}

class Route {
    // const shorcuts = array(
            // ':all' => '(.*)',
            // ':word' => '(\w+)',
            // ':number' => '([0-9]+)',
            // ':alpha' => '([a-zA-Z0-9-_]+)',
            // );
    public $shorcuts;        
    public $is_match = FALSE;
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
        $names  = array();
        $values = array();
        
        $url = strtr($url, $this->shorcuts);
        
        preg_match_all('#:([\w+]+)#', $url, $names, PREG_PATTERN_ORDER);
        $names = $names[0];
    
        $callback = array($this,'_clean_pattern');
        $pattern = preg_replace_callback('#:([\w-]+)#', $callback, $url);//.'/?';
        
        if(preg_match("#^\/?{$pattern}/?$#", $request, $values))
        {
            array_shift($values);
            
            foreach($names as $index => $value) 
                $this->params[substr($value, 1)] = urldecode($values[$index]);
            foreach ($this->target as $key => $value)
                $this->params[$key] = $value;
            
            $this->is_match = TRUE;
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
            ':alpha' => '([a-zA-Z0-9-_]+)',
        );
    }
    
    private function _clean_pattern($matches)
    {   
        $key = $matches[1];
        if(array_key_exists($key, $this->_conditions))
        {
            $out = '(?P<'.$key.'>'.$this->_conditions[$key].')';
        }
        else
        {
            $out = "(?P<{$key}>[a-zA-Z0-9_\-\.\!\~\*\\\'\(\)\:\@\&\=\$\+,%]+)";   
        }
        return $out;
    }
    
}
