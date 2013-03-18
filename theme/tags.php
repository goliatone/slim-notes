
<?php if( !isset($articles) || count($articles) === 0):?>
    <h3>Sorry, no articles matching your criteria!</h3>
<?php else:?>
    <h3>Notes under <?php echo $tag;?>  </h3>
    <ul>
    <?php foreach($articles as $slug => $article):?>
        <li>
            <h3><?php echo FlatG::a($article->title ,array('href' => FlatG::$config['base_url'].'note/'.$article->slug));?></h3>
            <strong>Date: <?php echo date($article->date)?></strong>
        </li>
    <?php endforeach;?>
    </ul>
<?php endif;?>

<ul>
<?php foreach($tags as $t =>$related):
    if($tag === $t) continue;
    ?>
    <li>
        <a href="<?php echo FlatG::$router->generate('tag', array('tag' => $t )) ;?>">
            <?php echo $t;?>
        </a>
    </li>
<?php endforeach;?>
</ul>