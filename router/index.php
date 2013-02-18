<?php
require '../flatg/flatg.php';

$path = pathinfo(__FILE__);
        
$config = array(
    'view_dir' => $path['dirname']."/../theme/",
    'articles_path' => $path['dirname']."/../articles/",
    'asset_path' => "/slimG/assets/",
    'layout' => 'layout',
    'featured_article' => 'hello-world',
    'router' => array(
        'basePath' => '/slimG/router'
     ),
     'base_url' => 'http://localhost/slimG/router/'
    // 'basePath'
);

FlatG::initialize($config);

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
       $archives = $articles;
    }
    
    $params['archives'] = $archives;
    
    FlatG::render('archives', $params);
};

$category_handler = function($params){
    FlatG::render('category', $params);
};

$tag_handler = function($params){
    FlatG::render('tag', $params);
};

FlatG::map('/', $index_handler , array('methods' => 'GET', 'name'=>'home'));

FlatG::map('/archives(/:year((/:month)(/:day)))',
             $archives_handler, 
             array('methods' => 'GET', 
                   'name' => 'archives',
                   'filters' => array('year' => '(19|20\d\d)',
                                      'month' => '([1-9]|[01][0-9])',
                                      'day' => '([1-9]|[01][0-9])'
                                      
                                     )
             )
        );

FlatG::map('/tag/:tag',
             $tag_handler, 
             array('methods' => 'GET', 
                   'name' => 'tag',
             )
        );
        
FlatG::map('/category/:category', 
             $category_handler, 
             array('methods' => 'GET', 
                   'name' => 'category', 
             ));
             
FlatG::map('/note/:slug', 
             $article_handler, 
             array( 'name'=>'note',
                    'filters' => array( 'slug' => '(.*)')
             )
          );

FlatG::run();

?>

   
