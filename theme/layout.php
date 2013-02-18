<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Project</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

        <link rel="stylesheet" href="<?php FlatG::assetUri('css/foundation.min.css');?>">
        <link rel="stylesheet" href="<?php FlatG::assetUri('css/main.css');?>">
        <script src="<?php FlatG::assetUri('js/vendor/modernizr.min.js');?>"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. 
                Please <a href="http://browsehappy.com/">upgrade your browser</a> or 
                <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> 
                to improve your experience.</p>
        <![endif]-->
        
        <!--CONTENT-->
        <!-- Header -->

        <!-- <header class="hero">
            <div class="twelve columns">
                <div class="three columns"></div>
                <div class="six columns">
                    <h1>This is the Theme!</h1>
                </div>
                <div class="three columns"></div>
            </div>
        </header> -->
        <!-- End Header -->
        <!-- Navigation -->

        <nav class="top-bar" >
            <ul>
                <li class="name">
                    <h1><a href="<?php echo FlatG::$router->generate('home');?>">Goliatone</a></h1>
                </li>
                <li class="toggle-topbar"><a href="#"></a></li>
            </ul>
            <section>
                <ul class="left">
                    <li><a href="<?php echo FlatG::$router->generate('tag', array('tag' => 'yotomanager' )) ;?>">Tags</a></li>
                    <li><a href="<?php echo FlatG::$router->generate('category', array('category' => 'yotomanager' )) ;?>">Categories</a></li>
                    <li><a href="<?php echo FlatG::$router->generate('archives', array('tag' => 'yotomanager' )) ;?>">Archives</a></li>
                </ul> 
                <ul class="right">
                    <li class="has-dropdown">
                        <a href="#">Link</a>
                        <ul class="dropdown">
                            <li><a href="#">Dropdown Link</a></li>
                            <li><a href="#">Dropdown Link</a></li>
                            <li><a href="#">Dropdown Link</a></li>
                        </ul>
                    </li>
                </ul>
            </section>
        </nav>
        <div class="top-bar-pad"></div>
        <!-- End Navigation -->
        <!-- Three-up Content Blocks -->
        
        <!-- Add your site or application content here -->
        <div class="row">
            <div class="eight columns offset-by-two">
                <?php if(isset($content)) echo $content;?>
            </div>
        </div>
        
        <?php echo FlatG::renderView('footer', $data);?>
        <!--@CONTENT-->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.8.2.min.js"><\/script>')</script>
<!--         <script src="js/plugins.js"></script> -->
<!--         <script src="js/main.js"></script> -->
    
        <?php if(isset($analytics_code)): ?>
        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
        <script>
            var _gaq=[['_setAccount',<?php $analytics_code;?>],['_trackPageview']];
            (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
            s.parentNode.insertBefore(g,s)}(document,'script'));
        </script>
        <?php endif;?>
    </body>
</html>