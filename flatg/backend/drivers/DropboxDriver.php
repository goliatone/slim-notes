<?php

/**
 * 
 */
class DropboxDriver /*extends AnotherClass*/
{
	public $service;
    
    public $files;
    public $filesMeta;
    public $output_path;
    
    //TODO: Move to config!
    public $file_extension = 'yaml';
    
	public function __construct($config) {
		$this->bootstrap($config);
	}
    
    public function bootstrap($config)
    {
        $path = $config['vendor'];
        // spl_autoload_unregister(array('YiiBase','autoload'));
        spl_autoload_register(function($class) use($path){
            $class = str_replace('\\', '/', $class);
            $path = realpath($path.DIRECTORY_SEPARATOR.$class . '.php');
            require_once($path);
        });
        // spl_autoload_register(array('YiiBase','autoload'));
        
        $this->folder = (empty($config['folder']) ? '/' : $config['folder']);
        
        $key      = $config['key'];
        $secret   = $config['secret'];
        $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
        $callback = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        
        $encrypter = new \Dropbox\OAuth\Storage\Encrypter('XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX');
        
        $storage = new \Dropbox\OAuth\Storage\Session($encrypter);
        
        $OAuth = new \Dropbox\OAuth\Consumer\Curl($key, $secret, $storage, $callback);
        $this->service = new \Dropbox\API($OAuth);
        
    }
    
    public function listFiles($refresh = FALSE, $path = FALSE)
    {
        if(isset($this->files) && ! $refresh) return $this->files;
        
        if(! $path)
            $path = $this->folder;
        
        //The path to the file/folder, relative to root
        $meta = $this->service->metaData($path);
        
        $this->filesMeta = $meta['body']->contents;
        $this->files = array();
        foreach($this->filesMeta as $file)
        {
            $info = pathinfo($file->path);
            if($info['extension'] !== $this->file_extension) continue;
            $this->files[ $info['filename'] ] = $file;    
        }
        
        return $this->files;
    }
    
    public function totalFiles()
    {
        if(! isset($this->filesMeta)) $this->listFiles();
        return count($this->files);
    }
    
    /**
     * md5_file
     * TODO: Use dropbox delta API!!!
     */
    public function sync($local = TRUE)
    {
        /*
        $response = $this->service->delta();
        
        echo "<p>This is some delta stuff.</p><pre><br/>";
        print_r($response);
        echo "</pre><p>--------------------------</p><br/>";
        */
        
        //make sure we have the file list.
        $this->listFiles();
        
        //load the local directory, and see how many files we have.
        $path = $this->output_path;
        
        $dir = new DirectoryIterator($path);
        
        $push_to_store   = array();
        $sync_with_store = array();
        $pull_from_store = array();
        
        foreach($dir as $local){
            if($local->isFile()){
                $info    = pathinfo($local->getBasename());
                
                if($info['extension'] !== $this->file_extension) continue;
                
                //if the local file is tracked:
                if(array_key_exists($local->getBasename('.'.$this->file_extension), $this->files))
                {
                    //Check if remote and local file are in sync. We use date.
                    $remote_file = $this->files[$info['filename']];
                    $remote = new DateTime($remote_file->modified, new DateTimeZone('UTC'));
                    
                    //local is out of sync
                    if($local->getMTime() < $remote->getTimestamp())
                    {
                        $pull_from_store[] = $remote_file;
                    } 
                    //remote is out of sync
                    else if( $local->getMTime() > $remote->getTimestamp())
                    {
                        //here we are also adding files that were 
                        //downloaded after a sync. 
                        $sync_with_store[] = $remote_file;   
                    }
                }
                //the file does not exist locally, fetch. 
                else 
                {
                    //*we seem to loose the reference if we store $local(?)
                    $push_to_store[] = array( 'path'=>$local->getPathname(),
                                              'name'=>$local->getBasename()
                                            );
                }
             }
        }
        
        //go over all remote files
        foreach($this->files as $remote)
        {
            //if the file is tracked locally, continue.
            if(file_exists($path.$remote->path)) continue;
            $pull_from_store[] = $remote;
        }
        echo "<pre>PULL FROM STORE";
        print_r($pull_from_store);
        echo "</pre>";
        
        //if we have files to pull, do so.
        if(isset($pull_from_store))
        {
            //TODO: Make its own method.
            foreach($pull_from_store as $remote)
            {
                //TODO: We can have the case were remote was just updated locally
                //if we run it seguido.
                $file = $this->service->getFile($remote->path, FALSE);
                $file_name = $path.DIRECTORY_SEPARATOR.$file['name'];
                /*
                $fh = fopen($path, 'w');
                fwrite($fh, $file['data']);
                fclose($fh);
                */
                
                file_put_contents($file_name, $file['data']);
                
                //TODO: Check for debug environment.
                //We have this so we can locally edit the file...
                chmod($file_name, 0664);
            }
        }
        
        //if remote file does not exist, push.
        if(isset($push_to_store))
        { 
            foreach($push_to_store as $local)
            {
                $this->service->putFile($local['path'], $local['name'], $this->folder);
            }
        }
        
        //this files were in remote and local store.
        if(isset($sync_with_store))
        {
            foreach($sync_with_store as $file)
            {
                $file = $this->service->getFile($remote->path, FALSE);
                $remote_content = $file['data'];
                $file_name = $path.DIRECTORY_SEPARATOR.$file['name'];
                // $file_name = $path;
                $local_content  = file_get_contents($file_name);
                //we only update if the content is different.
                if($remote_content === $local_content) continue;
                file_put_contents($file_name, $remote_content );
            }
        }
    }
}
