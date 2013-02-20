<!--pre>
    <?php print_r($note);?>
</pre-->

<h3><?php echo $note->title?></h3>
<span><?php echo $note->date;?></span>
<?php 
    if(isset($note->tags)):
?>
<br/>
<?php foreach($note->tags as $tag) echo  FlatG::a(FlatG::b($tag), array( 'href' => FlatG::$config['base_url'].'tag/'.$tag)).' ';?>
<?php endif;?>

<?php /*echo $note->parsedContent()*/;?>
<?php echo FlatG::$markdown->transform($note->content);?>

<ul>
<?php foreach($articles as $slug => $article):?>
    <?php if($article->title === $note->title) continue;?>
    <li>
        <h3><?php echo FlatG::a($article->title , array( 'href' => FlatG::$config['base_url'].'note/'.$article->slug));?></h3>
        <strong>Date: <?php echo date($article->date)?></strong>
    </li>
<?php endforeach;?>
</ul>