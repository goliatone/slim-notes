<!-- Footer -->
<footer class="row">
    <div class="twelve columns"><hr />
        <div class="row">
            <div class="six columns">
                <p>&copy; 2012 Goliatone | Made with: <?php echo FlatG::version(); ?></p>
            </div>
            <div class="six columns">
                <ul class="link-list right">
                    <li><a href="<?php echo FlatG::$router->generate('tag', array('tag' => 'yotomanager' )) ;?>">Tags</a></li>
                    <li><a href="<?php echo FlatG::$router->generate('category', array('category' => 'yotomanager' )) ;?>">Categories</a></li>
                    <li><a href="<?php echo FlatG::$router->generate('archives', array('year' => '2013', 'month'=>'03', 'day'=>'17' )) ;?>">Archives</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<!-- End Footer -->