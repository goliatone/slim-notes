<?php 
require_once('router.php');
require_once('route.php');
require_once('view.php');
require_once('article_model.php');
require_once('vendors/spyc/Spyc.php');
require_once('vendors/markdown/markdown.php');


define('G_VERSION',"FlatG v0.0.1");
define('G_LINK', 'http://goliatone.com');

class FlatG {
    
    static public $config = array();
    
    static public $router;
    static public $articles;
    static public $markdown;
    
    
    // static public function initialize(&$config)
    static public function initialize($config)
    {
        self::$config = $config;
        
        ArticleModel::$parser = new Spyc();
        ArticleModel::$path = $config['articles_path'];
        self::$articles = ArticleModel::fetch( );
        
        
   
        $parser_class = MARKDOWN_PARSER_CLASS;
        $parser = new $parser_class;
        self::$markdown = $parser;
        
        //TODO: make this for realz.
        self::$router = new Router();
        self::$router->setBasePath($config['router']['basePath']);
    }
    
    static public function featuredArticle()
    {
        return self::$config['featured_article'];
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
    
    static public function version($link = TRUE)
    {
        if($link) 
            return "<a href='".G_LINK."'>".G_VERSION."</a>";
        
        return G_VERSION;
    }
    
    static public function synchronize()
    {
        
        require_once ('backend/Storage.php');
        
        if(array_key_exists('default', self::$config['backend_storage']))
        {
            $backend_id = self::$config['backend_storage']['default']; 
        }
        
        $config = self::$config['backend_storage'][ $backend_id ];
        $config['vendor'] = $config['vendor'];
        $config['output_path'] = self::$config['articles_path'];
        
        $storage = Storage::build($config);
        
        // $store
        $files = $storage->listFiles();
        echo $storage->totalFiles();
        echo $storage->sync();
        
        // date_default_timezone_set('UTC');
        // date_default_timezone_set($reset);
        echo "<pre>Files listed<br/>";
        print_r($files);
    }
    
    static public function dump($var)
    {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }
    
//////////////////////////////////////////////////////////////////
////// MOVE TO HTML HELPER.
//////////////////////////////////////////////////////////////////
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

class GHelper 
{
    static public function merge_as_objects($source, $expand)
    {
        //We could also do a simple one liner...
        // return (object) array_merge((array) $source, (array) $expand);
        
        if(is_array($expand)) $expand = self::array_to_object($expand);
        if(is_array($source)) $source = self::array_to_object($source);
        
        foreach($expand as $k => $v) $source->$k = $v;
        
        return $source;
    }
    
    /**
     * 
     */
    static public function array_to_object($array, &$obj = FALSE)
    {
        
        if(!$obj)
            $obj = new stdClass();
            
        foreach ($array as $key => $value)
        {
            //TODO: Ensure $key has a valid format!
            
            if (is_array($value))
            {
                $obj->$key = new stdClass();
                self::array_to_object($value, $obj->$key);
            }
            else
            {
                $obj->$key = $value;
            }
        }
        return $obj;
     }
    
    /**
     * 
     */
    static public function appendFilenameToPath($source, $target)
    {
        $path_info = pathinfo($source);
        $file_name = $path_info['filename'].'.'.$path_info['extension'];
        return self::removeTrailingSlash($target, DS).DS.$file_name;   
    }
    
    /**
     * 
     */
    static public function removeTrailingSlash($path, $slash = '/')
    {
        return rtrim($path, $slash);
    }

    /**
     * 
     */
    static public function removeFilesFromDir($path, $ext = 'png')
    {
        $path = self::removeTrailingSlash($path, DS).DS;
        
        $files = glob("{$path}*.{$ext}");
        
        foreach($files as $file)
            @unlink($file);            
     }
    
    
}
