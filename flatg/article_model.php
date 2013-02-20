<?php

class ArticleModel
{
    /**
     * 
     */
    static public $CONTENT_DELIMETER = "\n\n";
    
    /**
     * 
     */
    static public $parser;
    
    static public $articles;
    static public $path;
    
    public $is_new = TRUE;
    public $_vo = array();
    
    static public function build($vo = array(), $template = array())
    {
        $data = array();
        
        if(isset($vo))
        {
            foreach($template as $item)
            {
                $data[$item] = array_key_exists($item, $vo) ? $vo[$item] : NULL;
            }
        } 
        else 
        {
            $data = $template;        
        }
        
        $model = new ArticleModel($data);
        return $model;
    }
    
    /**
     * 
     */
    public function __construct($vo = array())
    {
        $this->load($vo);
    }
    
    /**
     * 
     */
    public function load($vo)
    {
       $this->_vo = $vo;
       
       GHelper::array_to_object($vo, $this);
    }
    
    public function isNewRecord()
    {
        return $this->is_new;
    }
    
    static public function fetch($path = FALSE)
    {
        if(!$path) $path = self::$path;
        
        $dir = new DirectoryIterator($path);
        $articles = array();
        foreach($dir as $file){
            if($file->isFile()){
                $info    = pathinfo($file->getBasename());
                $handle  = fopen($path.DIRECTORY_SEPARATOR.$file->getFilename(), 'r');
                $content = stream_get_contents($handle);
                $content = explode(self::$CONTENT_DELIMETER, $content);
                $rawMeta = array_shift($content);
                $meta    = self::$parser->load($rawMeta);
                $meta['content'] = implode(self::$CONTENT_DELIMETER, $content);
                
                $model = new ArticleModel($meta);
                $model->is_new = FALSE;
                $articles[$info['filename']] = $model;
                
                // $articles[$info['filename']] = $this->module->yaml->loadFile($path.DS.$file->getFileName()); ;
             }
        }
        
        self::$articles = $articles;
        
        return $articles;
    }
    
    /**
     * 
     */
    static public function findBy($attribute,$value, $index = FALSE)
    {
        $i = 0;
        $indexed = NULL;
        
        foreach(self::$articles as $article)
        {
            if($article->$attribute === $value) return $article;
            if($i === $index) $indexed = $article;
        }
        
        return $indexed;
    }
    
}