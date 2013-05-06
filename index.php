<?php
/*
 * TODO: Header management, so we can take care of 304 and
 *       all that good stuff.
 */

require 'flatg/flatg.php';

$config = require('config/main.php');

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

/***********************************************
 * ADMIN: trigger sync.
 * TODO: We should take a secret key.
 *       https://github.com/fkooman/php-simple-auth/
 **********************************************/
$sync_handler = function($params)
{
    //This is really all we have to do to sync.
    //We should check headers, if ajax, return json with
    //status. Else, html, render OK/KO status.
    //Also, we should some how check for password?!!
    FlatG::synchronize();
};

FlatG::map('/admin/sync', 
            $sync_handler, 
            array( 'name'=>'sync'
            )
          );
/***********************************************
 * API
 * http://phpmaster.com/creating-a-php-oauth-server/
 * https://code.google.com/p/oauth-php/
 **********************************************/
$api_article_handler = function($params){
    $slug = $params['slug'];
    $file = ArticleModel::findBy('slug',$slug, 0);     
    $note = new ArticleModel($file);
    
    FlatG::renderJSON($note);
    
};
FlatG::map('/api/note/:slug', 
           $api_article_handler, 
           array( 'name'=>'api.note.get',
                  'filters' => array( 'slug' => '(.*)')
           )
);
$api_index_handler = function($params){
    $params['count'] = count(FlatG::$articles);
    $output = array();
    
    foreach(FlatG::$articles as $slug => $model )
    {
        $note = new stdClass();
        $note->title = $model->title;
        $note->slug = $model->slug;
        $note->date = $model->date;
        $note->file = $model->getFilename();
        $output[] = $note;
    }
    $params['notes'] = $output;
    FlatG::renderJSON($params);
};

FlatG::map('/api/notes', 
            $api_index_handler, 
            array( 'name'=>'api.notes.get',
                   'methods'=> 'GET'
            )
          );
$api_notes_full_handler = function($params){
    $params['count'] = count(FlatG::$articles);
    $params['notes'] = FlatG::$articles;
    
    FlatG::renderJSON($params);
};

FlatG::map('/api/notes/all', 
            $api_notes_full_handler, 
            array( 'name'=>'api.notes.get',
                   'methods'=> 'GET'
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

