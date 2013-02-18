<?php


class Storage
{
    
    public static function build($config)
    {
        $_DriverClass = Yii::import($config['class']);
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
