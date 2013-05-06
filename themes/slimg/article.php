<h3><?php echo $note->title?></h3>
<span><?php echo $note->date;?></span>
<?php 
    if(isset($note->tags)):
?>
<br/>
<?php foreach($note->tags as $tag) echo  FlatG::a(FlatG::b($tag), array( 'href' => FlatG::$config['base_url'].'tags/'.$tag)).' ';?>
<?php endif;?>
<?php /*echo $note->parsedContent()*/;?>
<?php echo FlatG::$markdown->transform($note->content);?>