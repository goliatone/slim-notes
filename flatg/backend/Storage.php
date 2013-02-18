<?php


class Storage
{
    
    public static function build($config)
    {
        // include_once($config['class']);
        // include_once('/Users/emilianoburgos/Development/www/slimG/flatg/backend/drivers/DropboxDriver.php');
        include_once($config['class']);
        
        $_DriverClass = rtrim( pathinfo($config['class'], PATHINFO_FILENAME), '.php');
        // $_DriverClass = $_DriverClass['']; 
        $driver = new $_DriverClass($config);
        $driver->output_path = $config['output_path'];
        
        $vendor = $config['vendor'];
        set_include_path(get_include_path().PATH_SEPARATOR.$vendor);
        
        return $driver;
    }
    
    public function __construct()
    {
        
    }
}
