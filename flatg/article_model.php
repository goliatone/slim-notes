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
    static public $indexed_meta = array();
    static public $indexable_metas = array('tags', 'categories','title', 'slug');
    
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
                
                self::indexMeta($meta, $model);
                
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
            if($i++ === $index) $indexed = $article;
        }
        
        return $indexed;
    }
    
    /**
     * 
     */
    static public function findAllByMeta($meta_attribute, $value)
    {
        $out = array();
        
        foreach(self::$articles as $article)
        {
            if(!isset($article->{$meta_attribute})) continue;
            
            $meta = $article->{$meta_attribute};
            
            if(is_string($meta))
            {
                if($meta === $value) array_push($out, $article);
            }
            else if(is_array($meta) || is_object($meta))
            {
                foreach($meta as $m)
                {
                    if($m === $value) array_push($out, $article);
                }
            } 
        }
        
        return $out;
    }
    
    /**
     * 
     */
    static public function indexMeta($metadata, $model)
    {
        foreach($metadata as $meta => $meta_value)
        {
            //We only want to go over indexable metadata.
            if(! in_array($meta, self::$indexable_metas)) continue;
            
            //for now we only index collection of metas
            if(is_string($meta_value))
            {
                self::storeModelByMeta($meta, $meta_value, $model);
            } 
            else if(is_array($meta_value) || is_object($meta_value))
            {
                foreach($meta_value as $index => $meta_key)
                {
                    self::storeModelByMeta($meta, $meta_key, $model);
                }
            } 
        }
    }
    
    static public function storeModelByMeta($meta, $meta_value, $model)
    {
        if(!isset(self::$indexed_meta[$meta]))
            self::$indexed_meta[$meta] = array();
        
        if(!isset(self::$indexed_meta[$meta][$meta_value]))
            self::$indexed_meta[$meta][$meta_value] = array();
        
        array_push(self::$indexed_meta[$meta][$meta_value], $model);
    }
    
    /**
     * 
     */
    static public function sortByDate($articles)
    {
        $results    = array();
        foreach($articles as $article){
            $date = new DateTime($article->date);
            $timestamp = $date->getTimestamp();
            $timestamp = array_key_exists($timestamp, $results) ? $timestamp + 1 : $timestamp;
            $results[$timestamp] = $article;
        }
        krsort($results);
        return $results;
    }
    
}