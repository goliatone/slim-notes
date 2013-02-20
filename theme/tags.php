<h3>Tags</h3>
<?php if( !isset($articles) || count($articles) === 0):?>
    <h3>Sorry, no articles matching your criteria!</h3>
<?php else:?>
    <ul>
    <?php foreach($articles as $slug => $article):?>
        <li>
            <h3><?php echo FlatG::a($article->title ,array('href' => FlatG::$config['base_url'].'note/'.$article->slug));?></h3>
            <strong>Date: <?php echo date($article->date)?></strong>
        </li>
    <?php endforeach;?>
    </ul>
<?php endif;?>