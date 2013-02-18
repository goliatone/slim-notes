<?php 

class GithubDriver
{
    public $service;
    public $files;
    
    
    
    public $repo;
    public $branch;
    
    public function __construct($config) {
        $this->bootstrap($config);
    }
    
    public function bootstrap($config)
    {
        $this->repo = $config['repo'];
        $this->branch = $config['branch'];
        
        $path  = $config['vendor'];
        $class = 'githuboauth.class';
        require_once($path.DIRECTORY_SEPARATOR.$class . '.php');
        
        $key      = $config['key'];
        $secret   = $config['secret'];
        // ID & Secret shown on your Github app's page.
        $this->service = new GithubOAuth( $key, $secret );
        
        
        if(array_key_exists('code', $_GET))
        {
            // Retrieve access code from URL after redirect, and exchange for access token
            $this->service->setTokenFromCode( $_GET['code'] );
        } 
        else 
        {
            // Scope of permissions requested
            $scope = array( 'user', 'repo', 'gist' );
            // Requests access & redirects to the URL defined in your Github application's settings
            $this->service->requestAccessCode( $scope );
        }
        
    }
    
    // https://api.github.com/repos/goliatone/jii/git/trees/gh-pages
    // https://api.github.com/repos/goliatone/jii/git/blobs/73f3a5cb5399493181f0db911bfc232837fe57ac
    public function listFiles($refresh = FALSE)
    {
        if(isset($this->files) && ! $refresh) return $this->files;
        
        // $meta = $this->service->metaData();
//         
        // $this->filesMeta = $meta['body']->contents;
        // $this->files = array();
        // foreach($this->filesMeta as $file)
        // {
            // $info = pathinfo($file->path);
            // if($info['extension'] !== $this->file_extension) continue;
            // $this->files[ $info['filename'] ] = $file;    
        // }
//         
        // return $this->files;
    }
    
    public function totalFiles()
    {
        // if(! isset($this->filesMeta)) $this->listFiles;
        // return count($this->files);
    }
    
    /**
     * md5_file
     * 
     */
    public function sync($local = TRUE)
    {
        $username = 'goliatone';
        $repository = 'jii';
        return $this->service->executeRequest(HTTPVerbs::GET, '/repos/'.$username.'/'.$repository);
    }
}
