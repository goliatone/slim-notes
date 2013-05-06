<?php if(count($archives) === 0):?>
    <h3>Sorry, no articles matching your criteria!</h3>
<?php endif;?>
<ul>
<?php foreach($archives as $slug => $article):?>
    <li>
        <h3><?php echo FlatG::a($article->title ,array('href' => FlatG::$config['base_url'].'note/'.$article->slug));?></h3>
        <strong>Date: <?php echo date($article->date)?></strong>
    </li>
<?php endforeach;?>
</ul>