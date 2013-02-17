<?php


class View
{
    const FILE_EXTENSION = '.php';
    
    public static $_global_data;
    
    /**
     * 
     * @private
     */
    protected $_partials;
    
    /**
     * 
     * @private
     */
    protected $_viewDirectory;
    
    /**
     * 
     */
    protected $_filename;
    
    public $data;
    
    /**
     * 
     */
    public function __construct($filename = FALSE)
    {
       $this->data = array();
       
       $this->_partials = array(); 
       
       if($filename)
       {
           $this->setFilename($filename);
       }
    }
    
    /**
     * 
     */
    public function render($filename, $data = array())
    {
        if(is_array($filename) && empty($data))
        {
            //we assigned $filename on construct.
            $data = $filename;
        } else if( is_string($filename))
        {
            $this->setFilename($filename);
        }
        $this->data = $data;
        
        foreach ($this->_partials as $viewName => $view) {
            // $view->set("layout", $this);
            $this->data[$viewName] = $view->render();
        }
        
        extract($this->data, EXTR_SKIP);

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
            include $this->getPath();
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
    
    /**
     * 
     */
    public function getViewDirectory()
    {
         return $this->_viewDirectory;
    }
    
    /**
     * 
     */
    public function setViewDirectory($dir)
    {
        if(!is_dir($dir)) throw new Exception("Invalid view directory: {$dir}");
        //im assuming that we are on nix :)
        $this->_viewDirectory = rtrim($dir, '/');
        
        return $this;
    }
    
    public function setFilename($filename)
    {
        $this->_filename = $filename.self::FILE_EXTENSION;
        return $this;
    }
    
    public function getFilename()
    {
        return $this->_filename;
    }
    
    /**
     * Add a view using a given name. 
     * @param string $name Name of the view used in the layout
     * @param View $view The instance to be associated with the name
     */
    public function addPartial($name, View $view) {
        $this->_partials[$name] = $view;
        
        return $this;
    }
    
    
    
    /**
     * 
     */
    public function getPath($filename = FALSE)
    {
        return $this->_viewDirectory.DIRECTORY_SEPARATOR.$this->getFilename(); 
    }
    
    
    
    /**
     * 
     */
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
        
        return $this;
    }
    
    /**
     * 
     */
    public function bindGlobal($key, & $value)
    {
        View::$_global_data[$key] =& $value;
        
        return $this;
    }
}