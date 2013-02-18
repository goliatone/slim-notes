<?php

/**
 * Retreive the metadata for a file/folder
 * @link https://www.dropbox.com/developers/reference/api#metadata
 * @link https://github.com/BenTheDesigner/Dropbox/blob/master/Dropbox/API.php#L170-192
 */

// Require the bootstrap
require_once('bootstrap.php');

// Set the file path
// You will need to modify $path or run putFile.php first
$path = 'api_upload_test.txt';
$path = '/';
// Get the metadata for the file/folder specified in $path
// $dropbox->setResponseFormat('json');
$metaData = $dropbox->metaData($path);
foreach( $metaData['body']->contents as $file) 
{
    $file = $dropbox->getFile($file->path, FALSE);
    echo "<br/>";
    echo "<br/>";
    // Dump the output
    var_dump($file);
    echo "<br/>";
    echo "<br/>";
}

// Dump the output
var_dump($metaData);
