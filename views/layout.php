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

<!--         <link rel="stylesheet" href="css/normalize.css"> -->
<!--         <link rel="stylesheet" href="css/main.css"> -->
        <!-- <script src="js/vendor/modernizr-2.6.2.min.js"></script> -->
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

        <!-- Add your site or application content here -->
        <?php echo $content;?>
        
        <p>Hello world! This is HTML5 Boilerplate.</p>
        <?php
        echo "<br/><ul>";
echo "<li><a href='/users'>Users empty</a></li>";
echo "<li><a href='/users/'>Users empty trailing slash</a></li>";
echo "<li><a href='/users/32'>Users 32</a></li>";
echo "<li><a href='/users/view/32'>Users action id</a></li>";
echo "<li><a href='/users/named/parameter'>Users named param</a></li>";
echo "<li><a href='/blog/tags/parameter'>Blog tAg</a></li>";
echo "</ul><hr/>";
?>
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