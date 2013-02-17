<?php echo $name;?>
<p>This is just some text, nothing more, nothing less.</p>
<p>Here, we should actually display the real deal, noting more, nothing less.</p>
<p>This is all folks! Nothing more, nothing less...or is it?!</p>

<?php 
$this->addPartial('footer', new View('footer', $this->data));
echo $this->getFilename();
echo FlatG::version();
?>
<pre>
<?php echo FlatG::getView('footer', $data);?>
</pre>