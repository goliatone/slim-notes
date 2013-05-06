<?php

$passwords = require('./settings.php');


//TODO: Move this to FlatG::get_base_path(); So we have global access
//and we dont polute this. Also, make a FlatG::import('path') so we can cache and 
//encapsulate logic...
$path = pathinfo(__FILE__);
$path = realpath($path['dirname'].DIRECTORY_SEPARATOR.'..');

//TODO: We need to move index from router, and then 
//handle/simplify all path management!! look into an 
//autoloader. Also, could we manage this in a helper?!
return array(
    'base_path' => $path,
    'view_dir' => $path."/themes/",
    'theme' => 'gone',
    'articles_extension' =>'yaml',
    'articles_path' => $path."/articles",
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
            'class' => $path.'/flatg/backend/drivers/DropboxDriver.php',
            'key'=>$passwords['dropbox']['key'],
            'secret'=>$passwords['dropbox']['secret'],
            'folder'=>'/articles/',
            'vendor'=> $path.'/flatg/vendors/dropbox'
            
        ),
        'github'=>array(
            'class' => $path.'/backend/drivers/GithubDriver.php',
            'key'=>$passwords['github']['key'],
            'secret'=>$passwords['github']['secret'],
            'repo'=>'https://github.com/goliatone/jii',
            'branch'=>'gh-pages',
            'vendor'=> $path.'/flatg/vendors/github-wrapper',
            
        ),
    ),
);

