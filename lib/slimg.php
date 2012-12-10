<?php 

class SlimG extends View
{
    public $router;
    public $request;
    public $response;
    public $name = '<h2>SlimG Text</h2>';
    
    public function __construct()
    {
        $this->init();
    }   
    
    public function init()
    {
        $this->request  = new Request();
        $this->response = new Response();
        $this->router   = new Router($this->request);
    }
    
    public function run(){
        $this->router->run();
    }
    
    /**
     * We need to extrapolate controller/action to view files.
     * - controller_dir
     *  | action_view
     *  | action_view
     */
    public function render($filename = 'layout.php', $data = array())
    {
        #This we should get from the controller action, here
        #we only deal with the real deal, the layout.
        $data['content'] = parent::render('articles.php');
        $content = parent::render($filename, $data);
        $this->response->html($content);
    }
}

define('ROUTER_DEFAULT_CONTROLLER', 'home');
define('ROUTER_DEFAULT_ACTION', 'index');
 
class Router {
    public $request;
    public $routes;
    public $controller, $controller_name;
    public $action, $id;
    public $params;
    public $routed = FALSE;
    public $callbacks;
    public $response;
    public function __construct($request) {
        $this->reset($request);
    }
    
    public function reset($request)
    {
        $this->request = $request;
        $this->routes = array();
        foreach(Request::$METHODS as $method)
            $this->routes[$method] = array();
        
        $this->callbacks = array();
        $this->response  = new Response();
    }
    
    /**
     * 
     */
    public function map($method, $rule, $target = array(), $conditions = array()) {
        //TODO: We have the request at this point, 
        // do we even need to process the routes that do not
        // match the request if $this->request->method !== $method return;
        if($this->request->method !== $method) return $this;
        if(! $this->request->is_valid_method($method)) return $this;
        
        $this->routes[$method][$rule] = new Route($rule, $this->request, $target, $conditions);
        
        return $this;
    }
    
    #TODO: Do we want to do this with __call?
    // public function get($rule, $target = array(), $conditions = array()) {
        // return $this->map(Request::GET, $rule, $target, $conditions);   
    // }
    
    public function __call($name, $arguments)
    {
        if(in_array(strtoupper($name), Request::$METHODS))
        {
            array_unshift($arguments, strtoupper($name));
            call_user_func_array(array($this, 'map'), $arguments);
        }
    }
 
    public function default_routes() {
        $this->map('/:controller');
        $this->map('/:controller/:action');
        $this->map('/:controller/:action/:id');
    }
 
    private function _route($route) {
        $this->routed = TRUE;
        $params = $route->params;
        
        if(isset($params['controller']))$this->controller = $params['controller']; 
        if(isset($params['action']))$this->action = $params['action']; 
        if(isset($params['id'])) $this->id = $params['id']; 
        
        if (empty($this->controller)) $this->controller = ROUTER_DEFAULT_CONTROLLER;
        if (empty($this->action)) $this->action = ROUTER_DEFAULT_ACTION;
        if (empty($this->id)) $this->id = NULL;
    
        if(!empty($_GET))
            $this->params = array_merge($params, $_GET);
        
        $this->request->params += $route->params;
        // $w = explode('_', $this->controller);
        // foreach($w as $k => $v) $w[$k] = ucfirst($v);
        // $this->controller_name = implode('', $w);
        #dispatch Event, route found. If not, we have to do a 404 boy.
        echo "Controller: ".$this->controller."<br/>";
        echo "Action: ".$this->action."<br/>";
        echo "Id: ".$this->id."<br/>";
        $this->response = array();
        $this->respose['msg'] = "Hola Mundo Mundial!<br/>";
        $this->dispatch($route->url, $this->request, $this->response);        
    }

    public function addListener($url, $callback)
    {
        $this->callbacks[$url] = $callback;
    }
    
    public function dispatch($url, $request, $reponse)
    {
        if(isset($this->controller) &&
           isset($this->action))
        {
            try
            {
                include_once('./controllers/'.$this->controller.'.php');
                $action     = $this->action;
                $controller = ucfirst($this->controller).'Controller';
                $controller = new $controller($request, $reponse);
                // $callback   = array($controller, $action);
                //we should get rid of the first 3 arguments?!
                // call_user_func_array($callback, $arguments);
                $controller->$action();
            }
            catch(Exception $e){};    
        }
        
        if(! array_key_exists($url, $this->callbacks))
            return $this->page_not_found('<p>We don\' have an actual listener!</p>');
        
        $arguments = func_get_args();
        array_shift($arguments);
        
        $callback = $this->callbacks[$url];
        
        call_user_func_array($callback, $arguments);
    }
 
    public function run() 
    {
        $routes = $this->routes[$this->request->method];
        foreach($routes as $route) {
            if(!$route->matched){ echo "NOT ".$route->pattern."<br/>";continue;}
            $this->_route($route);
            echo "YES<br/>";
            return $this;
        }
        
        $this->page_not_found('<p>End of Slimg::run method</p>');
        
        return $this;
    }
    
    /**
     * 
     */
    public function page_not_found($message = '')
    {
       echo "<h2>Page Not Found! :( </h2><pre>".$message;
       print_r($this->routes);
    }
}
 
class Route {
    
    // Defines the pattern of a <segment>
    const REGEX_KEY     = '<([a-zA-Z0-9_]++)>';

    // What can be part of a <segment> value
    const REGEX_SEGMENT = '[^/.,;?\n]++';

    // What must be escaped in the route regex
    const REGEX_ESCAPE  = '[.\\+*?[^\\]${}=!|]';
    
    public $matched = FALSE;
    public $params;
    public $url;
    public $pattern;
    public $conditions;
    public $name;
    
    /**
     * 
     */
    public function __construct($url, Request $request, $target, $conditions) {
        // $this->name = $name;
        //Ensure that all our $url's start with /
        if($url[0] !== '/') $url = '/'.$url;
        
        #TODO: Move to reset/init method...
        $this->url = $url;
        
        
        $this->params = array();
        $this->conditions = $conditions;
        $names = array(); $values = array();
        
        $this->shorcuts = array(
                ':all' => '(.*)',
                ':word' => '(\w+)',
                ':number' => '([0-9]+)',
                ':segment'=>'[^/]*',
                ':segments' => '[a-z0-9\-\_\/]+',
                ':alpha' => '([a-zA-Z-_]+)',
            );
        
        #make optional parts non capturing.
        if (strpos($url, '(') !== FALSE)
        {
            // Make optional parts of the URI non-capturing and optional
            $url = str_replace(array('(', ')'), array('(?:', ')?'), $url);
        }
            
        #replace all sugar named regexes:
        $pattern = strtr($url, $this->shorcuts);
        
        // Insert default regex for keys
        $pattern = str_replace(array('<', '>'), array('(?P<', '>'.Route::REGEX_SEGMENT.')'), $pattern);
        
        // $pattern = preg_replace('#'.Route::REGEX_ESCAPE.'#', '\\\\$0', $pattern);
        
        preg_match_all('#:([\w]+)#', $pattern, $names, PREG_PATTERN_ORDER);
        $names = $names[1];
        $pattern = preg_replace_callback('#:[\w]+#', array($this, 'clean_url'), $pattern);
        $pattern = "#^{$pattern}\/?$#uD";
        
        
        #TODO: We are missiong parameters that have no name!!!!!!
        #TODO: Move params to Request?!
        if (preg_match($pattern, $request->uri, $values)) {
            echo "-------<br/><pre>Names: ";
            print_r($names)."</pre><br/>";
            echo "-------<br/><pre>Values: ";
            print_r($values)."</pre><br/>";
            array_shift($values);
            //We should first loop over default values. 
            foreach($target as $key => $value) $this->params[$key] = $value;
            
            //Loop over named matches
            foreach($names as $index => $value) $this->params[$value] = $values[$value];
            
            $this->matched = TRUE;
            
            echo "We have a maching fucker!<Br/>";
            echo htmlentities($pattern)."<br/>Params:<br/> ";
            echo "<br/>".print_r($this->params);
        }
        
        $this->pattern = $pattern;
    }
 
    public function clean_url($matches) {
        $key = str_replace(':', '', $matches[0]);
    
        $conditions = "[a-zA-Z0-9_\-\.\!\~\*\\\'\(\)\:\@\&\=\$\+,%]+";
        // $conditions = "(\w)+";
    
        if (array_key_exists($key, $this->conditions)) {
            $conditions =  $this->conditions[$key];
        }
        
        $out = "(?P<{$key}>{$conditions})";
        
        return $out;
    }
}

/**
 * Request class to handle, uhm... requests
 */
class Request
{
    public static $POST_METHOD = '_m';
    public static $METHODS = array('GET', 'PUT', 'POST', 'DELETE');
    
    const GET    = 'GET';
    const PUT    = 'PUT';
    const POST   = 'POST';
    const DELETE = 'DELETE';
    
	public $uri;
    public $method;
    public $params;
    
	function __construct() {
	   $this->init();
	}
    
    public function init()
    {
        $this->params = array();
        
        #Build uri, clean querystring.
        $uri = $_SERVER['REQUEST_URI'];
        if(($pos = strpos($uri, '?'))) $uri = substr($uri, 0, $pos);
        
        $this->uri = $uri;
        
        #Get method
        $m = array_key_exists('REQUEST_METHOD', $_SERVER) ? $_SERVER['REQUEST_METHOD'] : NULL;
        #Hack on browsers not attaching PUT/DELETE.
        if($m == "POST" && array_key_exists(self::$POST_METHOD, $_POST)) 
            $m = strtoupper($env['POST'][self::$POST_METHOD]);
        
        echo "Request::method is = {$m}<br/>";
        
        $this->method = $m;
        
    }
    
    public function is_valid_method($method)
    {
        return in_array($method, self::$METHODS);
    }
    
    public function is_ajax()
    {
        $header = 'HTTP_X_REQUESTED_WITH';
        if(array_key_exists($header, $_SERVER) && 
          strtolower($_SERVER[$header]) == 'xmlhttprequest')
        {
            return TRUE;
        }
        return FALSE;   
    }
    public function __toString()
    {
        return "Request, url:".$this->uri." method: ".$this->method;
    }
}

class Dispatcher
{
    public $events = array();
    
    public function __construct()
    {
        $this->events = array();
    }
    
    public function bind($event, $callback, $obj = NULL) {
        if ( ! $this->events[$event]) {
            $this->events[$event] = array();
        }
    
        $this->events[$event][] = ($obj === NULL)  ? $callback : array($obj, $callback);
    }
  
    public function trigger($event) {
        if (! $this->events[$event]) return;
        
        $arguments = func_get_args();
        array_shift($arguments);
        
        ;
        
        foreach($this->events[$event] as $callback) {
            if(call_user_func_array($callback, $arguments) === FALSE) break;
        }
    }
}

class View extends Dispatcher
{
    public static $_global_data;
    
    public function getFilename($filename)
    {
        $path = pathinfo(__FILE__);
        return $path['dirname']."/../views/".$filename; 
    }
    
//     https://github.com/kimble/simple-php-template-engine/blob/master/lib/ComplexView.php
    public function render($filename, $data = array())
    {
        extract($data, EXTR_SKIP);

        if (View::$_global_data)
        {
            // Import the global view variables to local namespace
            extract(View::$_global_data, EXTR_SKIP | EXTR_REFS);
        }

        // Capture the view output
        ob_start();

        try
        {
            // Load the view within the current scope
            include $this->getFilename($filename);
        }
        catch (Exception $e)
        {
            // Delete the output buffer
            ob_end_clean();

            // Re-throw the exception
            throw $e;
        }

        // Get the captured output and close the buffer
        return ob_get_clean();
        
    }
    
    public function setGlobal($key, $value = NULL)
    {
        if (is_array($key))
        {
            foreach ($key as $key2 => $value)
            {
                View::$_global_data[$key2] = $value;
            }
        }
        else
        {
            View::$_global_data[$key] = $value;
        }
    }
    
    public function bindGlobal($key, & $value)
    {
        View::$_global_data[$key] =& $value;
    }
}

class Response
{
    public function html($content, $code = 200)
    {
        header('Content-type: text/html; charset=utf-8');
        echo $content;
        exit;
    }
    /**
     * 
     */
    public function json($obj, $code = 200) {
        header('Content-type: application/json', TRUE, $code);
        echo json_encode($obj);
        exit;
    }
}