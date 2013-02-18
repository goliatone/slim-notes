<?php
require '../flatg/flatg.php';

$path = pathinfo(__FILE__);
        
$config = array(
    'view_dir' => $path['dirname']."/../theme/",
    'asset_path' => "/slimG/assets/",
    'layout' => 'layout',
    'router' => array(
        'basePath' => '/slimG/router'
     )
    // 'basePath'
);

FlatG::initialize($config);

$article_handler = function($params){
    $params['name'] = $params['slug'];
    $params['footer'] = FlatG::renderView('footer', $params);
    FlatG::render('article', $params);  
};

$index_handler = function($params){
    FlatG::render('home', $params);  
};

$archives_handler = function($params){
    FlatG::render('archives', $params);
};

$category_handler = function($params){
    FlatG::render('category', $params);
};

$tag_handler = function($params){
    FlatG::render('tag', $params);
};

FlatG::map('/', $index_handler , array('methods' => 'GET', 'name'=>'home'));

FlatG::map('/archives(/:year(/:month))',
             $archives_handler, 
             array('methods' => 'GET', 
                   'name' => 'archives',
                   'filters' => array('year' => '(19|20\d\d)',
                                      'month' => '([1-9]|[01][0-9])'
                                      
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

   
