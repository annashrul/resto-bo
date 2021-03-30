<?php 
$myfile = fopen(base_url()."assets/transfile/test.txt", "r") or die("Unable to open file!");
echo fread($myfile,filesize("webdictionary.txt"));
fclose($myfile);
?>