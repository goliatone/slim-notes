<?php
require 'flatg/flatg.php';

//TODO: Header management, so we can take care of 304 and
//all that good stuff.

$path = pathinfo(__FILE__);
//TODO: We need to move index from router, and then 
//handle/simplify all path management!! look into an 
//autoloader. Also, could we manage this in a helper?!
$config = array(
    'base_path' => $path['dirname'],
    'view_dir' => $path['dirname']."/theme/",
    'articles_extension' =>'yaml',
    'articles_path' => $path['dirname']."/articles",
    'asset_path' => "/slimG/assets/",
    'layout' => 'layout',
    'featured_article' => 'hello-world',
    'router' => array(
        'basePath' => '/slimG'
     ),
    'base_url' => 'http://localhost/slimG/',
    'backend_storage' =>array(
        'default'=>'dropbox',
        'dropbox'=>array(
            // 'class' => $path['dirname'].'/backend/drivers/DropboxDriver.php',
            'class' => $path['dirname'].'/flatg/backend/drivers/DropboxDriver.php',
            'key'=>'xxxxxxxxxxxxx',
            'secret'=>'xxxxxxxxxxxxx',
            'folder'=>'/articles/',
            'vendor'=> $path['dirname'].'/flatg/vendors/dropbox'
            
        ),
        'github'=>array(
            'class' => $path['dirname'].'/backend/drivers/GithubDriver.php',
            'key'=>'xxxxxxxxxxxxx',
            'secret'=>'xxxxxxxxxxxxx',
            'repo'=>'https://github.com/goliatone/jii',
            'branch'=>'gh-pages',
            'vendor'=> $path['dirname'].'/flatg/vendors/github-wrapper',
            
        ),
    ),
);

FlatG::initialize($config);

$sync_handler = function($params)
{
    //This is really all we have to do to sync.
    //We should check headers, if ajax, return json with
    //status. Else, html, render OK/KO status.
    //Also, we should some how check for password?!!
    FlatG::synchronize();
};

$article_handler = function($params){
    $params['articles'] = FlatG::$articles;
    
    $slug = $params['slug'];
    //If we have slug, try to retrieve it. If we
    //don't have a slug or the slug is invalid, get
    //a default value. TODO: We should be able to 
    //filter posts by.
    $file = ArticleModel::findBy('slug',$slug, 0);     
    $params['note'] = new ArticleModel($file);
    FlatG::render('article', $params);  
};

$index_handler = function($params){
        
    $params['articles'] = FlatG::$articles;
    
    $slug = FlatG::featuredArticle();
    //If we have slug, try to retrieve it. If we
    //don't have a slug or the slug is invalid, get
    //a default value. TODO: We should be able to 
    //filter posts by.
    $file = ArticleModel::findBy('slug',$slug, 0);     
    $params['note'] = new ArticleModel($file);
        
    FlatG::render('home', $params);  
};

$archives_handler = function($params){
    #Move to GHelper::get_arguments()
    
    $archives = array();
    $articles = FlatG::$articles;
    
    //TODO: Move to helper class:
    $args = array();
    $keys = array('year', 'month', 'day');
    foreach($keys as $key)
    {
        if(key_exists($key, $params))
            $args[$key] = $params[$key];
    }
    //
    
    $dateFormat = function($args, $format){
        $temp_date = is_array($args) ? implode('-', $args) : $args;
        $date   = new DateTime($temp_date);
        return $date->format($format);
    };

    if(count($args)>0) {
        switch(count($args)){
            case 1 :    //only year is present
                $format = 'Y';
            break;
            case 2 :    //year and month are present
                $format = 'Y-m';
            break;
            case 3 : //year, month and date are present
                $format = 'Y-m-d';
            break;
        }
        
        $date = $dateFormat($args,$format);
        // filter articles
        foreach($articles as $article){
            if($dateFormat($article->date, $format) === $date){
                $archives[] = $article;
            }
        }
    }
    else
    {
        //         
        $archives = ArticleModel::sortByDate($articles);
    }
    
    $params['archives'] = $archives;
    
    FlatG::render('archives', $params);
};

$category_handler = function($params){
    
    FlatG::render('category', $params);
};

$tags_handler = function($params){
    
    $params['articles'] = array();
    
    //If we are actually looking for a tag:
    if(array_key_exists('tag', $params))
    {
        $tags = $params['tag'];
        $params['articles'] = ArticleModel::findAllByMeta('tags', $tags);        
    }
    
    //we want to show all the tags.
    $params['tags'] = ArticleModel::$indexed_meta['tags'];
    // FlatG::dump(ArticleModel::$indexed_meta);
    
    
    FlatG::render('tags', $params);
};

FlatG::map('/admin/sync', 
            $sync_handler, 
            array( 'name'=>'sync'
            )
          );

//ADD ROUTES AND HANDLERS.
//Home Route.
FlatG::map('/', $index_handler , array('methods' => 'GET', 'name'=>'home'));

//Archives Route.
FlatG::map('/archives(/:year((/:month)(/:day)))',
             $archives_handler, 
             array('name' => 'archives',
                   'filters' => array('year' => '(19|20\d\d)',
                                      'month' => '([1-9]|[01][0-9])',
                                      'day' => '([1-9]|[01][0-9])'
                                      
                                     )
             )
        );
        
//Tags Route.
FlatG::map('/tags(/:tag)',
             $tags_handler, 
             array('name' => 'tag')
        );

//Category Route.        
FlatG::map('/category(/:category)', 
             $category_handler, 
             array('name' => 'category')
          );

//Note Route.           
FlatG::map('/note/:slug', 
             $article_handler, 
             array( 'name'=>'note',
                    'filters' => array( 'slug' => '(.*)')
             )
          );
          
//Page Route.
FlatG::map('/:slug', 
            $article_handler, 
            array( 'name'=>'page',
                'filters' => array( 'slug' => '(.*)')
            )
          );

//Let's fire this BadBoy :)   
FlatG::run();

