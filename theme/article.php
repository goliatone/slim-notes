<h3><?php echo $note->title?></h3>
<span><?php echo $note->date;?></span>

<?php /*echo $note->parsedContent()*/;?>
<?php echo FlatG::$markdown->transform($note->content);?>